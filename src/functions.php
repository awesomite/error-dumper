<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Views\ViewCli;
use Awesomite\ErrorDumper\Views\ViewHtml;

if (!\function_exists('Awesomite\ErrorDumper\exception_dump')) {
    /**
     * @codeCoverageIgnore
     *
     * @param \Exception|\Throwable $exception
     */
    function exception_dump($exception)
    {
        $view = 'cli' === \php_sapi_name()
            ? new ViewCli()
            : new ViewHtml();

        $view->display(new SerializableException($exception));
    }
}
