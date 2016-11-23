<?php

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface;

interface ErrorDumperInterface
{
    /**
     * @return ErrorHandlerInterface
     */
    public function getErrorHandler();

    public function displayHtml(ClonedExceptionInterface $exception, EditorInterface $editor = null);

    public function displayCli(ClonedExceptionInterface $exception);
}