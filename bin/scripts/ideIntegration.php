<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Views\ViewHtml;

$handler = new ErrorHandler();

$view = new ViewHtml();
$view->setEditor(new Phpstorm());
$handler->pushListener(new OnExceptionDevView($view));

$handler->exitAfterTrigger(true);
$handler->register();

\trigger_error('Test error', \E_USER_ERROR);
