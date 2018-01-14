<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
