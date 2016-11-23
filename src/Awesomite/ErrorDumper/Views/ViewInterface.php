<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\StackTrace\StackTraceInterface;

interface ViewInterface
{
    /**
     * @param ClonedExceptionInterface $exception
     * @return mixed
     */
    public function display(ClonedExceptionInterface $exception);
}