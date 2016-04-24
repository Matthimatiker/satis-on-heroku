<?php

namespace Matthimatiker\SatisOnHeroku;

use Composer\Config;
use Composer\Json\JsonFile;
use Symfony\Component\HttpFoundation\Request;

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
$config->merge(array('config' => $satisConfigFile->read()));

var_dump($possibleRepositoryUrls, $config->getRepositories());
