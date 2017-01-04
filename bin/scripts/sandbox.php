<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler()
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

$sandbox = $errorHandler->getErrorSandbox();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test');
});

return 'No errors ;)';