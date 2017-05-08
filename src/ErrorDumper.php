<?php

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface;
use Awesomite\ErrorDumper\Listeners\ListenerDevView;
use Awesomite\ErrorDumper\Views\ViewCli;
use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\ErrorDumper\Views\ViewInterface;

class ErrorDumper
{
    /**
     * @param int $mode Default E_ALL | E_STRICT
     * @param int $policy Default ErrorHandler::POLICY_ERROR_REPORTING
     * @param EditorInterface|null $editor
     * @return ErrorHandlerInterface
     *
     * @see ErrorHandler::POLICY_ERROR_REPORTING
     * @see ErrorHandler::__construct
     */
    public static function createDevHandler($mode = null, $policy = null, EditorInterface $editor = null)
    {
        $handler = new ErrorHandler($mode, $policy);
        $handler->pushListener(new ListenerDevView(self::createDefaultView($editor)));

        return $handler;
    }

    /**
     * @param EditorInterface|null $editor
     * @return ViewInterface
     */
    private static function createDefaultView(EditorInterface $editor = null)
    {
        if (php_sapi_name() === 'cli') {
            return new ViewCli();
        }

        // @codeCoverageIgnoreStart
        $view = new ViewHtml();
        if ($editor) {
            $view->setEditor($editor);
        }

        return $view;
        // @codeCoverageIgnoreEnd
    }
}