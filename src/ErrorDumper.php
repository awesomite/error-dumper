<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Views\ViewCli;
use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\ErrorDumper\Views\ViewInterface;

class ErrorDumper
{
    /**
     * @param int                  $mode   Default E_ALL | E_STRICT
     * @param EditorInterface|null $editor
     *
     * @return ErrorHandlerInterface
     *
     * @see ErrorHandler::__construct
     */
    public static function createDevHandler($mode = null, EditorInterface $editor = null)
    {
        $handler = new ErrorHandler($mode);
        $handler->pushListener(new OnExceptionDevView(self::createDefaultView($editor)));

        return $handler;
    }

    /**
     * @param EditorInterface|null $editor
     *
     * @return ViewInterface
     */
    private static function createDefaultView(EditorInterface $editor = null)
    {
        if ('cli' === \php_sapi_name()) {
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
