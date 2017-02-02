<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Sandboxes\SandboxException;
use Symfony\Component\HttpFoundation\Response;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler();
$errorHandler->register();

try {
    $sandbox = $errorHandler->getErrorSandbox();
    $sandbox->execute(function () {
        trigger_error('test');
    });

    return 'OK';
} catch (SandboxException $exception) {
    $body = 'Error message: ' . $exception->getMessage() . "\n";
    $body .= 'Error code: ' . $exception->getCode() . "\n";
    $body .= 'Location in code: ' . $exception->getFile() . ':' . $exception->getLine();

    $response = new Response();
    $response->headers->set('Content-Type', 'text/plain');
    $response->setCharset('UTF-8');
    $response->setContent($body);

    return $response;
}
