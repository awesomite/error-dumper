<?php

namespace Awesomite\ErrorDumper\Listeners;

/**
 * @internal
 */
class AppendableString
{
    private $string;

    public function __construct($string = '')
    {
        $this->string = $string;
    }

    public function append($string)
    {
        $this->string .= ($this->string === '' ? '' : "\n") . $string;

        return $this;
    }

    public function __toString()
    {
        return $this->string;
    }
}
