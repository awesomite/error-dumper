<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Listeners;

interface OnExceptionInterface
{
    /**
     * @param \Throwable|\Exception $exception
     *
     * @return void
     */
    public function onException($exception);
}
