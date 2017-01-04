<?php

namespace Awesomite\ErrorDumper\Listeners;

class ListenerClosure implements ListenerInterface
{
    private $callable;

    /**
     * ListenerCallable constructor.
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