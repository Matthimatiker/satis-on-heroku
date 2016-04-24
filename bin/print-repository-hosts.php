<?php

namespace Matthimatiker\SatisOnHeroku;

/**
 * Prints the host names of all configured package repositories, one host per line.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

$config = new SatisConfig();
echo implode(PHP_EOL, $config->getRepositoryHosts());
