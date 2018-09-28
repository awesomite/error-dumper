<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Listeners\PreExceptionCallable;
use Awesomite\ErrorDumper\Listeners\StopPropagationException;
use Awesomite\ErrorDumper\Views\ViewFactory;

$preListener = new PreExceptionCallable(function ($exception) {
    /** @var \Exception|\Throwable $exception */
    if ($exception instanceof \RuntimeException) {
        echo '<strong>Exception will be not caught</strong>';

        throw new StopPropagationException();
    }
});

$errorHandler = new ErrorHandler(\E_ALL ^ \E_USER_DEPRECATED);
$errorHandler->pushListener(new OnExceptionDevView(ViewFactory::create()));

/**
 * preListener can stop propagation next listeners
 */
$errorHandler->pushPreListener($preListener);

$errorHandler->register();

throw new \RuntimeException('Test exception!');
