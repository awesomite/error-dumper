<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler();
$errorHandler->register();

$sandbox = $errorHandler->getErrorSandbox();

trigger_error('Test error', E_USER_WARNING);
return 'OK';
