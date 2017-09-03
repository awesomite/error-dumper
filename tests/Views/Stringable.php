<?php

namespace Awesomite\ErrorDumper\Views;

/**
 * @internal
 */
class Stringable
{
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function __toString()
    {
        return call_user_func($this->callable);
    }
}
