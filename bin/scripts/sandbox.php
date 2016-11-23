<?php

use Awesomite\ErrorDumper\DevErrorDumper;

$errorDumper = new DevErrorDumper();
$errorHandler = $errorDumper->getErrorHandler();
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

$sandbox = $errorHandler->getErrorSandbox();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test');
});

return 'No errors ;)';