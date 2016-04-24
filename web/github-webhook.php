<?php

if (!isset($_POST['payload'])) {
    echo 'Missing payload.';
    exit();
}

$payload = json_decode($_POST['payload'], true);
if (!isset($payload['repository']['full_name'])) {
    echo 'Expected repository name in payload.';
    exit();
}

var_dump($payload);
