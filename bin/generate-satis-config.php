<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$repositories = array_filter($_SERVER, function ($key) {
    return strpos($key, 'SATIS_REPOSITORY_') === 0;
}, ARRAY_FILTER_USE_KEY);

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../views');
$twig = new \Twig_Environment($loader);

$config = $twig->render('satis.json.twig', array(
    'env' => $_SERVER,
    'repositories' => $repositories
));
file_put_contents(__DIR__ . '/../satis.json', $config);
