<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
        // implement whatever logic you need to determine the asset path

        return sprintf('http://localhost:8000/%s', ltrim($asset, '/'));
    }));

    return $twig;
}));

$app->get('/', function() use ($app){
//    return 'Testes do Silex';
    return $app['twig']->render('index.twig');
})
->bind('home');

$app->get('/ola/{name}', function ($name) use ($app) {
    return $app['twig']->render('ola.twig', array(
                'name' => $name,
    ));
})
->bind('ola');

$blogPosts = array(
    1 => array(
        'date' => '2011-03-29',
        'author' => 'igorw',
        'title' => 'Using Silex',
        'body' => 'teste',
    ),
);

$app->get('/blog', function() use ($blogPosts) {
    $output = '';
    foreach ($blogPosts as $post) {
        $output .= $post['title'];
        $output .= '<br>';
    }
    return $output;
})
->bind('blog');

$app->get('/blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if (!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id nao existe.");
    }

    $post = $blogPosts[$id];

    return "<h1>{$post['title']}</h1>" .
            "<p>{$post['body']}</p>";
});


$app->post('/feedback', function (Request $request){
    $message = $request->get('message');
    mail('maykondeykon@gmail.com', 'Teste Silex', $message);
    
    return new Response('Obrigado por seu retorno',201);
});

$app->get('/navigation', function () use ($app) {
    return '<a href="'.$app['url_generator']->generate('home').'">Home</a>'.
           ' | '.
           '<a href="'.$app['url_generator']->generate('blog').'">Blog</a>'.
        ' | '.
           '<a href="'.$app['url_generator']->generate('ola', array('name' => 'Maykon')).'">Ola Igor</a>';
});

$app->run();
