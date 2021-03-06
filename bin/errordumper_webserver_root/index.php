<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

\error_reporting(E_ALL | \E_STRICT);
\ini_set('display_errors', 1);
\date_default_timezone_set('Europe/Warsaw');

list($root) = \explode(\DIRECTORY_SEPARATOR . 'bin' . \DIRECTORY_SEPARATOR, __DIR__);
require_once $root . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'autoload.php';

$pages = array(
    'exceptionChain'         => 'Exception chain',
    'testStackTrace'         => 'Stack trace',
    'sandbox'                => 'Sandbox',
    'sandbox2'               => 'Sandbox with exception',
    'skipChosenErrors'       => 'Skip chosen errors',
    'humanFriendlyErrorCode' => 'Human friendly error code',
    'skipRuntimeException'   => 'Skipping chosen exceptions',
    'fatalError'             => 'Fatal error',
    'ideIntegration'         => 'IDE integration',
    'symfonyVarDumper'       => 'Symfony VarDumper',
);

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app['debug'] = true;

$onExit = null;
$app
    ->get('/{page}', function ($page) use ($app, $pages, &$onExit) {
        if ('skipRuntimeException' === $page) {
            $onExit = function () use ($page) {
                require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'scripts', $page . '.php'));
            };

            return false;
        }

        if (\in_array($page, \array_keys($pages))) {
            return require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'scripts', $page . '.php'));
        }

        return $app['twig']->render('index.twig', array(
            'pages' => $pages,
        ));
    })
    ->bind('homepage')
    ->value('page', 'index');

$app->run();

if ($onExit) {
    \call_user_func($onExit);
}
