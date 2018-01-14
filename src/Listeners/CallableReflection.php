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
class CallableReflection
{
    /**
     * @var \ReflectionFunctionAbstract
     */
    private $reflection;

    private $isThrowable = null;

    /**
     * @var \ReflectionClass|null
     */
    private $throwableReflection = null;

    /**
     * The following constructor does not validate input paramter,
     * $callable parameter MUST be callable.
     *
     * @param $callable
     */
    public function __construct($callable)
    {
        $this->reflection = $this->getReflection($callable);
    }

    /**
     * @param \Exception|\Throwable $exception
     *
     * @return bool
     */
    public function isThrowableCallableBy($exception)
    {
        if (!$this->isThrowableCallable()) {
            return false;
        }

        if (\is_null($this->throwableReflection)) {
            return true;
        }

        $className = \get_class($exception);

        return $this->throwableReflection->isSubclassOf($className)
            || ($this->throwableReflection->getName() === $className);
    }

    /**
     * First parameter must be Throwable or interface, others must be optional
     *
     * @return bool
     */
    public function isThrowableCallable()
    {
        if (\is_null($this->isThrowable)) {
            $this->isThrowable = $this->checkIsThrowableCallable();
        }

        return $this->isThrowable;
    }

    private function checkIsThrowableCallable()
    {
        $params = $this->reflection->getParameters();

        if (0 === \count($params)) {
            return true;
        }

        $first = \array_shift($params);

        if ($class = $first->getClass()) {
            $className = \version_compare(PHP_VERSION, '7.0') >= 0 ? 'Throwable' : 'Exception';
            if (!$class->isInterface() && !$class->isSubclassOf($className) && ($class->getName() !== $className)) {
                return false;
            }
            $this->throwableReflection = $class;
        } elseif (\version_compare(PHP_VERSION, '7.0') >= 0 && $first->hasType()) {
            return false;
        }

        if ($first->isArray()) {
            return false;
        }

        if (\version_compare(PHP_VERSION, '5.4') >= 0 && $first->isCallable()) {
            return false;
        }

        foreach ($params as $param) {
            if (!$param->isOptional()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param callable $callable
     *
     * @return \ReflectionFunctionAbstract
     */
    private function getReflection($callable)
    {
        if (\is_object($callable)) {
            return new \ReflectionMethod($callable, '__invoke');
        }

        if (\is_string($callable)) {
            if (false === \mb_strpos($callable, '::')) {
                return new \ReflectionFunction($callable);
            }

            $callable = \explode('::', $callable, 2);
        }

        return new \ReflectionMethod($callable[0], $callable[1]);
    }
}
