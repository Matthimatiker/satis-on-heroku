<?php

namespace Matthimatiker\SatisOnHeroku;

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

var_dump($payload);
