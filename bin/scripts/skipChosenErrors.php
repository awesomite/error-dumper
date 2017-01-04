<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler(E_ALL ^ E_USER_DEPRECATED);
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

$sandbox = $errorHandler->getErrorSandbox();

trigger_error('Test error', E_USER_DEPRECATED);
return 'OK';
