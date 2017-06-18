<?php

namespace Awesomite\ErrorDumper\Listeners;

class PreExceptionCallable implements PreExceptionInterface
{
    private $callable;

    public static function stopPropagation()
    {
        throw new StopPropagationException();
    }

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Argument has to be callable!');
        }

        $this->callable = $callable;
    }

    public function preException($exception)
    {
        call_user_func($this->callable, $exception);
    }
}
