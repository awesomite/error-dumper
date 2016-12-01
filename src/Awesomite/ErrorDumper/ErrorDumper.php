<?php

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Handlers\ErrorHandler;

class ErrorDumper extends AbstractErrorDumper
{
    /**
     * @codeCoverageIgnore
     *
     * ErrorDumper constructor.
     * @param int $mode Default E_ALL | E_STRICT
     * @param callable $event
     */
    public function __construct($event, $mode = null)
    {
        $this->errorHandler = new ErrorHandler($event, $mode);
    }
}
