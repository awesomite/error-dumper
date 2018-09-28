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
use Awesomite\ErrorDumper\Listeners\StopPropagationException;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\StandardExceptions\ErrorException;
use Awesomite\ErrorDumper\StandardExceptions\ShutdownErrorException;

class ErrorHandler implements ErrorHandlerInterface
{
    // Constant can be an array in PHP >=5.6
    private static $fatalErrors
        = array(
            \E_ERROR,
            \E_PARSE,
            \E_CORE_ERROR,
            \E_CORE_WARNING,
            \E_COMPILE_ERROR,
            \E_COMPILE_WARNING,
        );

    private $mode;

    private $sandbox = null;

    private $exitAfterTrigger = true;

    /**
     * @var OnExceptionInterface[]
     */
    private $listeners = array();

    /**
     * @var PreExceptionInterface[]
     */
    private $preListeners = array();

    /**
     * @param int $mode Default E_ALL | E_STRICT
     *
     * @see http://php.net/manual/en/errorfunc.constants.php
     *
     * @see http://php.net/manual/en/language.operators.bitwise.php
     */
    public function __construct($mode = null)
    {
        if (!\is_int($mode) && !\is_null($mode)) {
            throw new \InvalidArgumentException('Argument $mode has to be integer or null!');
        }

        $this->mode = \is_null($mode) ? \E_ALL | \E_STRICT : $mode;
    }

    public function register($types = ErrorHandlerInterface::TYPE_ALL)
    {
        if ($types & static::TYPE_ERROR) {
            $this->registerOnError();
        }

        // @codeCoverageIgnoreStart
        if ($types & static::TYPE_EXCEPTION) {
            $this->registerOnException();
        }

        if ($types & static::TYPE_FATAL_ERROR) {
            $this->registerOnShutdown();
        }

        // @codeCoverageIgnoreEnd

        return $this;
    }

    public function registerOnError()
    {
        \set_error_handler(array($this, static::HANDLER_ERROR), $this->mode);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnException()
    {
        \set_exception_handler(array($this, static::HANDLER_EXCEPTION));

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnShutdown()
    {
        \register_shutdown_function(array($this, static::HANDLER_SHUTDOWN));

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function exitAfterTrigger($condition)
    {
        $this->exitAfterTrigger = (bool)$condition;

        return $this;
    }

    public function getErrorSandbox()
    {
        if (\is_null($this->sandbox)) {
            $this->sandbox = new ErrorSandbox($this->mode);
        }

        return $this->sandbox;
    }

    public function handleError($code, $message, $file, $line)
    {
        if (($this->mode & $code) && ((\error_reporting() & $code))) {
            $this->onError(new ErrorException($message, $code, $file, $line));
        }
    }

    /**
     * @param \Exception|\Throwable $exception
     */
    public function handleException($exception)
    {
        $this->onError($exception);
    }

    public function handleShutdown()
    {
        $error = \error_get_last();
        if (!$error || !($error['type'] & $this->mode)) {
            return;
        }
        if ($this->isFatalError($error['type'])) {
            $this->onError(
                new ShutdownErrorException($error['message'], $error['type'], $error['file'], $error['line'])
            );
        }
    }

    public function pushListener(OnExceptionInterface $listener)
    {
        $this->listeners[] = $listener;

        return $this;
    }

    public function pushPreListener(PreExceptionInterface $preListener)
    {
        $this->preListeners[] = $preListener;

        return $this;
    }

    /**
     * @param \Exception|\Throwable $exception
     */
    private function onError($exception)
    {
        foreach ($this->preListeners as $validator) {
            try {
                $validator->preException($exception);
            } catch (StopPropagationException $exception) {
                return;
            }
        }

        foreach ($this->listeners as $event) {
            $event->onException($exception);
        }

        // @codeCoverageIgnoreStart
        if ($this->exitAfterTrigger) {
            exit(1);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @codeCoverageIgnore
     *
     * @param int $code
     *
     * @return bool
     */
    private function isFatalError($code)
    {
        foreach (self::$fatalErrors as $fatalCode) {
            if ($code & $fatalCode) {
                return true;
            }
        }

        return false;
    }
}
