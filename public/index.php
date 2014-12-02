<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$loader = new Twig_Loader_Filesystem('../views/template/base.html');
$twig = new Twig_Environment($loader);

$app->get('/', function() use ($twig) {

            return $twig->render('Hello {{ name }}!', array('name' => 'Fabien'));
        })
        ->bind('home');


$app->run();
