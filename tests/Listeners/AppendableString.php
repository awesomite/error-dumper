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
final class AppendableString
{
    private $string;

    public function __construct($string = '')
    {
        $this->string = $string;
    }

    public function append($string)
    {
        $this->string .= ('' === $this->string ? '' : "\n") . $string;

        return $this;
    }

    public function __toString()
    {
        return $this->string;
    }
}
