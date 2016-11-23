<?php

use Awesomite\ErrorDumper\DevErrorDumper;

$errorDumper = new DevErrorDumper(E_ALL ^ E_USER_DEPRECATED);
$errorHandler = $errorDumper->getErrorHandler();
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

$sandbox = $errorHandler->getErrorSandbox();

trigger_error('Test error', E_USER_DEPRECATED);
return 'OK';
