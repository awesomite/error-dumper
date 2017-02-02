<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Listeners\ValidatorClosure;

$validator = new ValidatorClosure(function ($exception) {
    /** @var \Exception|\Throwable $exception */
    if ($exception instanceof \RuntimeException) {
        echo '<strong>Exception will be not caught</strong>';
        ValidatorClosure::stopPropagation();
    }
});

$dumper = new ErrorDumper();
$handler = $dumper->createDevHandler()
    ->pushValidator($validator)
    ->register();

throw new \RuntimeException('Test exception!');