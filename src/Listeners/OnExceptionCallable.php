<?php

namespace Awesomite\ErrorDumper\Listeners;

class OnExceptionCallable implements OnExceptionInterface
{
    private $callable;

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

    public function onException($exception)
    {
        call_user_func($this->callable, $exception);
    }
}
