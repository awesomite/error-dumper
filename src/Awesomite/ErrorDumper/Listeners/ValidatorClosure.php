<?php

namespace Awesomite\ErrorDumper\Listeners;

class ValidatorClosure implements ValidatorInterface
{
    private $callable;

    /**
     * ValidatorCallable constructor.
     * @param callable $callable
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Argument has to be callable!');
        }

        $this->callable = $callable;
    }

    public function onBeforeException($exception)
    {
        call_user_func($this->callable, $exception);
    }

    public function stopPropagation()
    {
        throw new StopPropagationException();
    }
}