<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Listeners\ListenerInterface;
use Awesomite\ErrorDumper\Listeners\ValidatorInterface;
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
     * @param bool $condition
     * @return ErrorHandlerInterface
     */
    public function exitAfterTrigger($condition);

    /**
     * @param ListenerInterface $listener
     * @return ErrorHandlerInterface
     */
    public function pushListener(ListenerInterface $listener);

    /**
     * @param ValidatorInterface $validator
     * @return ErrorHandlerInterface
     */
    public function pushValidator(ValidatorInterface $validator);

    /**
     * @return ErrorSandboxInterface
     */
    public function getErrorSandbox();

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     */
    public function handleError($code, $message, $file, $line);

    /**
     * @param \Throwable|\Exception $exception
     * @return void
     */
    public function handleException($exception);

    public function handleShutdown();
}