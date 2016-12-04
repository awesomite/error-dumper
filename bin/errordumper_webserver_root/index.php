<?php

date_default_timezone_set('Europe/Warsaw');

list($root) = explode(DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR, __DIR__);
require_once $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$pages = array(
    'testStackTrace' => 'Stack trace',
    'sandbox' => 'Sandbox',
    'sandbox2' => 'Sandbox with exception',
    'skipChosenErrors' => 'Skip chosen errors',
    'humanFriendlyErrorCode' => 'Human friendly error code'
);

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app['debug'] = true;

$app
    ->get('/{page}', function ($page) use ($app, $pages) {
        if (in_array($page, array_keys($pages))) {
            return require implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'scripts', $page . '.php'));
        }

        return $app['twig']->render('index.twig', array(
            'pages' => $pages
        ));
    })
    ->bind('homepage')
    ->value('page', 'index')
;

$app->run();