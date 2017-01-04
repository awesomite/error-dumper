<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Listeners\ValidatorClosure;

$validator = new ValidatorClosure(function ($exception) use (&$validator) {
    /** @var \Exception|\Throwable $exception */
    /** @var ValidatorClosure $validator */

    if ($exception instanceof \RuntimeException) {
        echo '<strong>Exception will be not caught</strong>';
        $validator->stopPropagation();
    }
});

$dumper = new ErrorDumper();
$handler = $dumper->createDevHandler()

    ->pushValidator($validator)

    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

throw new \RuntimeException('Test exception!');