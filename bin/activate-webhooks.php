#!/usr/bin/env php
<?php

/**
 * Activates update webhooks for the configured GitHub repositories.
 */

namespace Matthimatiker\SatisOnHeroku;

use Github\Client;

require_once(__DIR__ . '/../vendor/autoload.php');

$config = new SatisConfig();
$token = $config->getGitHubToken();
if ($token === null) {
    echo 'No GitHub token configured, cannot manage webhooks.' . PHP_EOL;
    exit(1);
}

$client = new Client();
$client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);
$webhooksApi = $client->repos()->hooks();

foreach ($config->getRepositoryUrls() as $url) {
    if ($url->getHost() !== 'github.com') {
        continue;
    }
    list($owner, $repository) = $url->getPathSegments();
    $repository = basename($repository, '.git');
    $activeHooks = $webhooksApi->all($owner, $repository);
    //var_dump($activeHooks);
}
