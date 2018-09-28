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

use Awesomite\ErrorDumper\Optimizer\Application as OptimizerApplication;

require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

OptimizerApplication::run();
