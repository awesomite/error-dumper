<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorDumper = new ErrorDumper();
$errorHandler = $errorDumper->createDevHandler()->register();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test');
});

return 'No errors ;)';