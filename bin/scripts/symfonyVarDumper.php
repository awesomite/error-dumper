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
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\StackTrace\StackTraceFactory;
use Awesomite\VarDumper\SymfonyVarDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;

$handler = new ErrorHandler();

$handler->pushListener(new OnExceptionCallable(function ($exception) {
    $stackTraceFactory = new StackTraceFactory(new SymfonyVarDumper(new CliDumper()));
    $serializable = new SerializableException($exception, 0, false, true, true, $stackTraceFactory);

    $view = new ViewHtml();
    $view->setEditor(new Phpstorm());

    $view->display($serializable);
}));

$handler->exitAfterTrigger(true);
$handler->register();

\trigger_error('Test error', \E_USER_ERROR);
