#!/usr/bin/env php
<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

\error_reporting(E_ALL | \E_STRICT);
\ini_set('display_errors', 1);

require_once \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, 'scripts', 'testStackTrace.php'));
