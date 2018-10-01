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
use Awesomite\ErrorDumper\Views\ViewFactory;

$errorHandler = new ErrorHandler();
$errorHandler->pushListener(new OnExceptionDevView(ViewFactory::create()));
$errorHandler->register();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    \trigger_error('test');
});

return 'No errors ;)';
