<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Sandboxes\SandboxException;
use Symfony\Component\HttpFoundation\Response;

$errorHandler = ErrorDumper::createDevHandler();
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
