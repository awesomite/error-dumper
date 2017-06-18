<?php

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
