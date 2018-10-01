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
abstract class AbstractExceptionEvent
{
    private $callable;

    /**
     * @var null|CallableReflection
     */
    private $reflection = null;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(\sprintf('Argument passed to %s must be callable', __METHOD__));
        }

        $this->callable = $callable;
    }

    /**
     * @param \Exception|\Throwable $exception
     */
    protected function call($exception)
    {
        $reflection = $this->getReflection();
        if (
            !$reflection->hasFirstParam()
            || !$reflection->hasFirstParamClassType()
            || $reflection->isFirstParamClassPassTo($exception)
        ) {
            \call_user_func($this->callable, $exception);
        }
    }

    private function getReflection()
    {
        return $this->reflection ?: $this->reflection = new CallableReflection($this->callable);
    }
}
