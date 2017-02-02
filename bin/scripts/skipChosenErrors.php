<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler(E_ALL ^ E_USER_DEPRECATED);
$errorHandler->register();

trigger_error('Test error', E_USER_DEPRECATED);
return 'OK';
