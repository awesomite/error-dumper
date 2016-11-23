<?php

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface;
use Awesomite\ErrorDumper\Views\ViewCli;
use Awesomite\ErrorDumper\Views\ViewHtml;

abstract class AbstractErrorDumper implements ErrorDumperInterface
{
    /**
     * @var ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @codeCoverageIgnore
     *
     * @param ClonedExceptionInterface $exception
     * @param EditorInterface|null $editor
     */
    public function displayHtml(ClonedExceptionInterface $exception, EditorInterface $editor = null)
    {
        $view = new ViewHtml();
        if (!is_null($editor)) {
            $view->setEditor($editor);
        }
        $view->display($exception);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param ClonedExceptionInterface $exception
     */
    public function displayCli(ClonedExceptionInterface $exception)
    {
        $view = new ViewCli();
        $view->display($exception);
    }

    public function getErrorHandler()
    {
        return $this->errorHandler;
    }
}