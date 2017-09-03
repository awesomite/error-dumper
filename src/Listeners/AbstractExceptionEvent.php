<?php

namespace Awesomite\ErrorDumper\Listeners;

abstract class AbstractExceptionEvent
{
    private $callable;

    private $reflection;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('Argument passed to %s must be callable', __METHOD__));
        }

        $reflection = new CallableReflection($callable);

        if (!$reflection->isThrowableCallable()) {
            $exceptionClass = version_compare(PHP_VERSION, '7.0') ? 'Throwable' : 'Exception';
            throw new \InvalidArgumentException(
                sprintf('Invalid callable, first argument must be %s or an interface', $exceptionClass)
            );
        }

        $this->callable = $callable;
        $this->reflection = $reflection;
    }

    /**
     * @param \Exception|\Throwable $exception
     */
    protected function call($exception)
    {
        if ($this->reflection->isThrowableCallableBy($exception)) {
            call_user_func($this->callable, $exception);
        }
    }
}
