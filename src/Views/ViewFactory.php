<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Views;

final class ViewFactory
{
    /**
     * @return ViewInterface
     */
    public static function create()
    {
        return 'cli' === \php_sapi_name() ? new ViewCli() : new ViewHtml();
    }
}
