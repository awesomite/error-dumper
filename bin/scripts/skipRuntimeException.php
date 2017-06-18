<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Listeners\PreExceptionCallable;

$preListener = new PreExceptionCallable(function ($exception) {
    /** @var \Exception|\Throwable $exception */
    if ($exception instanceof \RuntimeException) {
        echo '<strong>Exception will be not caught</strong>';
        PreExceptionCallable::stopPropagation();
    }
});

ErrorDumper::createDevHandler()
    ->pushPreListener($preListener)
    ->register();

throw new \RuntimeException('Test exception!');
