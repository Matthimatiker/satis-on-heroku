<?php

namespace Matthimatiker\SatisOnHeroku;

use Composer\Config;
use Composer\Json\JsonFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

require(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();

if (!$request->isMethod('POST')) {
    echo 'Expected POST request.';
    exit();
}

$payload = json_decode($request->getContent(), true);
if (!isset($payload['repository']['full_name'])) {
    echo 'Expected repository name in payload.';
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
    echo 'Cannot update, none of the following repositories is managed by this Satis instance: ' . implode(', ', $possibleRepositoryUrls);
    exit();
}
$repositoryToUpdate = current($matches);

$command = 'vendor/bin/satis build --repository-url=' . ProcessUtils::escapeArgument($repositoryToUpdate);
$process = new Process($command, __DIR__ . '/..');
$process->run(function ($type, $buffer) {
    if (Process::ERR === $type) {
        echo 'ERR > '.$buffer;
    } else {
        echo 'OUT > '.$buffer;
    }
});
