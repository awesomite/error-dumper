<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\ErrorDumper;

$errorHandler = ErrorDumper::createDevHandler()->register();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    \trigger_error('test');
});

return 'No errors ;)';
