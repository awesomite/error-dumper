<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Sandboxes\ErrorSandboxInterface;

interface ErrorHandlerInterface
{
    /**
     * @return ErrorHandlerInterface
     */
    public function registerOnError();

    /**
     * @return ErrorHandlerInterface
     */
    public function registerOnShutdown();

    /**
     * @return ErrorHandlerInterface
     */
    public function registerOnException();

    /**
     * @return ErrorSandboxInterface
     */
    public function getErrorSandbox();
}