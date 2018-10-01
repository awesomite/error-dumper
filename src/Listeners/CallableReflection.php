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
final class CallableReflection
{
    /**
     * @var \ReflectionFunctionAbstract
     */
    private $reflection;

    /**
     * The following constructor does not validate input parameter,
     * $callable parameter MUST be callable.
     *
     * @param $callable
     */
    public function __construct($callable)
    {
        $this->reflection = $this->getReflection($callable);
    }

    /**
     * @return bool
     */
    public function hasFirstParam()
    {
        return \count($this->reflection->getParameters()) > 0;
    }

    /**
     * @return null|bool
     */
    public function hasFirstParamClassType()
    {
        $params = $this->reflection->getParameters();
        if (0 === \count($params)) {
            return null;
        }

        return null !== $params[0]->getClass();
    }

    /**
     * @param \Exception|\Throwable $exception
     *
     * @return null|bool
     *
     * @return null
     */
    public function isFirstParamClassPassTo($exception)
    {
        $params = $this->reflection->getParameters();
        if (0 === \count($params)) {
            return null;
        }

        if (null === $paramClassReflection = $params[0]->getClass()) {
            return null;
        }

        $paramClass = $paramClassReflection->getName();

        return $exception instanceof $paramClass;
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
