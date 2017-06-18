<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Listeners\OnExceptionInterface;
use Awesomite\ErrorDumper\Listeners\PreExceptionInterface;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandboxInterface;

interface ErrorHandlerInterface
{
    /**
     * @param int $types
     * @return ErrorHandlerInterface
     */
    public function register($types = ErrorHandler::TYPE_ALL);
    
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
     * @param OnExceptionInterface $listener
     * @return ErrorHandlerInterface
     */
    public function pushListener(OnExceptionInterface $listener);

    /**
     * @param PreExceptionInterface $preListener
     * @return ErrorHandlerInterface
     */
    public function pushPreListener(PreExceptionInterface $preListener);

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
