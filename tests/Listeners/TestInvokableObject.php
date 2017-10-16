<?php

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
