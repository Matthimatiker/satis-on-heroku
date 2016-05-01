#!/usr/bin/env php
<?php

/**
 * Activates update webhooks for the configured GitHub repositories.
 */

namespace Matthimatiker\SatisOnHeroku;

use Github\Client;
use Guzzle\Http\Url;
use Matthimatiker\SatisOnHeroku\GitHub\WebhookManager;

require_once(__DIR__ . '/../vendor/autoload.php');

if (!isset($_SERVER['SATIS_URL']) || empty($_SERVER['SATIS_URL'])) {
    echo 'SATIS_URL not configured, cannot determine webhook URL.' . PHP_EOL;
    exit(1);
}

$config = new SatisConfig();
$token = $config->getGitHubToken();
if ($token === null) {
    echo 'No GitHub token configured, cannot manage webhooks.' . PHP_EOL;
    exit(1);
}

$webhookUrl = Url::factory(rtrim($_SERVER['SATIS_URL'], '/') . '/github-webhook.php');
if (isset($_SERVER['SATIS_AUTH_USERNAME']) && !empty($_SERVER['SATIS_AUTH_USERNAME'])) {
    $webhookUrl->setUsername($_SERVER['SATIS_AUTH_USERNAME']);
    $webhookUrl->setPassword(isset($_SERVER['SATIS_AUTH_PASSWORD']) ? $_SERVER['SATIS_AUTH_PASSWORD'] : '');
}
$client = new Client();
$client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);
$webhookManager = new WebhookManager($client->repos()->hooks(), $webhookUrl);

$exitCode = 0;
foreach ($config->getRepositoryUrls() as $url) {
    if (!$webhookManager->supports($url)) {
        continue;
    }
    echo 'Updating hook for ' . $url . '... ';
    try {
        $webhookManager->registerFor($url);
        echo 'Done.' . PHP_EOL;
    } catch (\Exception $e) {
        $exitCode = 1;
        echo 'Failed: ' . PHP_EOL;
        echo $e . PHP_EOL;
    }
}
exit($exitCode);
