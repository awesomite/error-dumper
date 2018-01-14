<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        return \call_user_func($this->callable);
    }
}
