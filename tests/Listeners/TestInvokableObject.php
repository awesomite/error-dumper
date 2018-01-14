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

/**
 * @internal
 */
class TestInvokableObject
{
    public function __invoke()
    {
    }

    public function doSomething()
    {
    }

    public static function doStaticAction()
    {
    }

    public static function handleStdClass(\stdClass $class)
    {
    }

    public static function handleArray(array $array)
    {
    }

    public static function notOptional(\Exception $exception, $param)
    {
    }
}
