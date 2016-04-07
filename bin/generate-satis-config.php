<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../views');
$twig = new \Twig_Environment($loader);

$config = $twig->render('satis.json.twig', array('env' => $_SERVER));
file_put_contents(__DIR__ . '/../satis.twig.json', $config);
