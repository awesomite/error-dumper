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

ErrorDumper::createDevHandler()->register();

\trigger_error('Test error', E_USER_WARNING);
return 'OK';
