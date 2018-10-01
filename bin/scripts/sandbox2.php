<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Sandboxes\SandboxException;
use Awesomite\ErrorDumper\Views\ViewFactory;
use Symfony\Component\HttpFoundation\Response;

$errorHandler = new ErrorHandler();
$errorHandler->pushListener(new OnExceptionDevView(ViewFactory::create()));
$errorHandler->register();

try {
    $sandbox = $errorHandler->getErrorSandbox();
    $sandbox->execute(function () {
        \trigger_error('test');
    });

    return 'OK';
} catch (SandboxException $exception) {
    $body = 'Error message: ' . $exception->getMessage() . "\n";
    $body .= 'Severity: ' . $exception->getSeverity() . "\n";
    $body .= 'Location in code: ' . $exception->getFile() . ':' . $exception->getLine();

    $response = new Response();
    $response->headers->set('Content-Type', 'text/plain');
    $response->setCharset('UTF-8');
    $response->setContent($body);

    return $response;
}
