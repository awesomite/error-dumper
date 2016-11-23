<?php

use Awesomite\ErrorDumper\DevErrorDumper;

$errorDumper = new DevErrorDumper();
$errorHandler = $errorDumper->getErrorHandler();
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

$sandbox = $errorHandler->getErrorSandbox();

trigger_error('Test error', E_USER_WARNING);
return 'OK';
