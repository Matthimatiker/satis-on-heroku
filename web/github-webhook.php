<?php

namespace Matthimatiker\SatisOnHeroku;

use Composer\Config;
use Composer\Json\JsonFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

require(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();
$response = new Response();

if (!$request->isMethod('POST')) {
    $response->setStatusCode(400);
    $response->setContent('Expected POST request.');
    $response->send();
    exit();
}

$payload = json_decode($request->getContent(), true);
if (!isset($payload['repository']['full_name'])) {
    $response->setStatusCode(400);
    $response->setContent('Expected repository name in payload.');
    $response->send();
    exit();
}
$repositoryName = $payload['repository']['full_name'];
$possibleRepositoryUrls = array(
    sprintf('git@github.com:%s.git', $repositoryName),
    sprintf('https://github.com/%s.git', $repositoryName),
);

$satisConfigFile = new JsonFile(__DIR__ . '/../satis.json');
$config = new Config();
$config->merge($satisConfigFile->read());
$configuredRepositories = array_map(function (array $repositoryData) {
    if (!isset($repositoryData['url'])) {
        return null;
    }
    return $repositoryData['url'];
}, $config->getRepositories());
$configuredRepositories = array_filter($configuredRepositories);

$matches = array_intersect($configuredRepositories, $possibleRepositoryUrls);
if (count($matches) === 0) {
    $response->setStatusCode(404);
    $response->setContent(
        'Cannot update, none of the following repositories is managed by this Satis instance: ' .
        implode(', ', $possibleRepositoryUrls)
    );
    $response->send();
    exit();
}
$repositoryToUpdate = current($matches);

$command = 'vendor/bin/satis build --no-interaction --repository-url=%s';
$command = sprintf($command, ProcessUtils::escapeArgument($repositoryToUpdate));
$process = new Process($command, __DIR__ . '/..');
$output = '';
$process->run(function ($type, $buffer) use (&$output) {
    $output .= $buffer;
});

$response->setStatusCode($process->isSuccessful() ? 200 : 500);
$response->setContent($output);
$response->send();
