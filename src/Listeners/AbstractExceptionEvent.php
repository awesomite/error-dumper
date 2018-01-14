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
        if ($this->getReflection()->isThrowableCallableBy($exception)) {
            \call_user_func($this->callable, $exception);
        }
    }

    private function getReflection()
    {
        if (\is_null($this->reflection)) {
            $this->reflection = new CallableReflection($this->callable);
        }

        return $this->reflection;
    }
}
