<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Listeners\OnExceptionInterface;
use Awesomite\ErrorDumper\Listeners\PreExceptionInterface;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandboxInterface;

interface ErrorHandlerInterface
{
    const TYPE_ERROR       = 1; // 0b0001
    const TYPE_EXCEPTION   = 2; // 0b0010
    const TYPE_FATAL_ERROR = 4; // 0b0100
    const TYPE_ALL         = 7; // 0b0111

    const HANDLER_ERROR     = 'handleError';
    const HANDLER_EXCEPTION = 'handleException';
    const HANDLER_SHUTDOWN  = 'handleShutdown';

    /**
     * @param int $types
     *
     * @return ErrorHandlerInterface
     */
    public function register($types = self::TYPE_ALL);

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
     *
     * @return ErrorHandlerInterface
     */
    public function exitAfterTrigger($condition);

    /**
     * @param OnExceptionInterface $listener
     *
     * @return ErrorHandlerInterface
     */
    public function pushListener(OnExceptionInterface $listener);

    /**
     * @param PreExceptionInterface $preListener
     *
     * @return ErrorHandlerInterface
     */
    public function pushPreListener(PreExceptionInterface $preListener);

    /**
     * @return ErrorSandboxInterface
     */
    public function getErrorSandbox();

    /**
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return void
     */
    public function handleError($code, $message, $file, $line);

    /**
     * @param \Exception|\Throwable $exception
     *
     * @return void
     */
    public function handleException($exception);

    public function handleShutdown();
}
