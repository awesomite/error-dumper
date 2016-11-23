<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\StandardExceptions\FatalErrorException;
use Awesomite\ErrorDumper\StandardExceptions\ShutdownErrorException;

/**
 * @internal
 */
class ErrorHandler implements ErrorHandlerInterface
{
    private $mode;

    private $event;

    private $sandbox = null;

    private $onErrorRegistered = false;

    private $onShutdownRegistered = false;

    /**
     * ErrorErrorHandler constructor.
     * @param callable $event
     * @param int $mode Default E_ALL | E_STRICT
     *
     * @see http://php.net/manual/en/errorfunc.constants.php
     */
    public function __construct($event, $mode = null)
    {
        if (!is_callable($event)) {
            throw new \InvalidArgumentException('Argument $event has to be callable!');
        }
        $this->event = $event;
        $this->mode = is_null($mode) ? E_ALL | E_STRICT : $mode;
    }

    public function registerOnError()
    {
        $self = $this;
        set_error_handler(function ($number, $str, $file, $line) use ($self) {
            $exception = new FatalErrorException();
            $exception
                ->setFile($file)
                ->setCodeAndMessage($number, $str)
                ->setLine($line);
            $self->onError($exception);
        }, $this->mode);
        $this->onErrorRegistered = true;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnException()
    {
        $self = $this;
        set_exception_handler(function ($exception) use ($self) {
            $self->onError($exception);
        });

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnShutdown()
    {
        $self = $this;
        $mode = $this->mode;
        register_shutdown_function(function () use ($self, $mode) {
            if ($error = error_get_last()) {
                if (!($error['type'] & $mode)) {
                    return;
                }
                $exception = new ShutdownErrorException();
                $exception
                    ->setFile($error['file'])
                    ->setCodeAndMessage($error['type'], $error['message'])
                    ->setLine($error['line']);
                $self->onError($exception);
            }
        });
        $this->onShutdownRegistered = true;

        return $this;
    }

    /**
     * @internal Method has to be public, because of PHP 5.3
     *
     * @param \Exception|\Throwable $exception
     */
    public function onError($exception)
    {
        call_user_func($this->event, $exception);
    }

    public function getErrorSandbox()
    {
        if (is_null($this->sandbox)) {
            if (!$this->onErrorRegistered && !$this->onShutdownRegistered) {
                throw new \LogicException('Register onError or onShutdown before calling this method!');
            }
            $this->sandbox = new ErrorSandbox($this->mode);
        }

        return $this->sandbox;
    }
}