<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Serializable;

final class Variable implements VariableInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dump;

    /**
     * @param string $name
     * @param string $dump
     */
    public function __construct($name, $dump)
    {
        $this->name = $name;
        $this->dump = $dump;
    }

    public function getName()
    {
        return $this->name;
    }

    public function dumpAsString()
    {
        return $this->dump;
    }

    public function serialize()
    {
        return \serialize(array($this->name, $this->dump));
    }

    public function unserialize($serialized)
    {
        list($this->name, $this->dump) = \unserialize($serialized);
    }
}
