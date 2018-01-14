<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\TestHelpers;

/**
 * @internal
 */
class Beeper
{
    private $counter = 0;

    public function beep()
    {
        $this->counter++;
    }

    public function reset()
    {
        $this->counter = 0;
    }

    public function countBeeps()
    {
        return $this->counter;
    }
}
