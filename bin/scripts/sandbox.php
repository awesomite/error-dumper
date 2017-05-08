<?php

use Awesomite\ErrorDumper\ErrorDumper;

$errorHandler = ErrorDumper::createDevHandler()->register();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test');
});

return 'No errors ;)';