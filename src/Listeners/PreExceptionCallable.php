<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Listeners;

class PreExceptionCallable extends AbstractExceptionEvent implements PreExceptionInterface
{
    public static function stopPropagation()
    {
        throw new StopPropagationException();
    }

    public function preException($exception)
    {
        $this->call($exception);
    }
}
