<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Listeners\ListenerInterface;
use Awesomite\ErrorDumper\Listeners\StopPropagationException;
use Awesomite\ErrorDumper\Listeners\ValidatorInterface;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\StandardExceptions\ErrorException;
use Awesomite\ErrorDumper\StandardExceptions\ShutdownErrorException;

class ErrorHandler implements ErrorHandlerInterface
{
    const HANDLER_ERROR = 'handleError';
    const HANDLER_EXCEPTION = 'handleException';
    const HANDLER_SHUTDOWN = 'handleShutdown';

    const POLICY_ERROR_REPORTING = 1;
    const POLICY_ALL = 2;

    // Constant can be an array in PHP >=5.6
    private static $fatalErrors = array(
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
    );

    private $mode;

    private $policy;

    private $sandbox = null;
    
    private $exitAfterTrigger = true;

    /**
     * @var ListenerInterface[]
     */
    private $listeners = array();

    /**
     * @var ValidatorInterface[]
     */
    private $validators = array();

    /**
     * ErrorErrorHandler constructor.
     * @param int $mode Default E_ALL | E_STRICT
     * @param int $policy Default ErrorHandler::POLICY_ERROR_REPORTING
     *
     * @see http://php.net/manual/en/errorfunc.constants.php
     */
    public function __construct($mode = null, $policy = null)
    {
        if (!is_int($mode) && !is_null($mode)) {
            throw new \InvalidArgumentException('Argument $mode has to be integer or null!');
        }
        if (!in_array($policy, array(static::POLICY_ERROR_REPORTING, static::POLICY_ALL, null), true)) {
            throw new \InvalidArgumentException('Invalid value of $policy!');
        }

        $this->mode = is_null($mode) ? E_ALL | E_STRICT : $mode;
        $this->policy = is_null($policy) ? static::POLICY_ERROR_REPORTING : $policy;
    }

    public function registerOnError()
    {
        set_error_handler(array($this, static::HANDLER_ERROR), $this->mode);

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnException()
    {
        set_exception_handler(array($this, static::HANDLER_EXCEPTION));

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public function registerOnShutdown()
    {
        register_shutdown_function(array($this, static::HANDLER_SHUTDOWN));

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param bool $condition
     * @return $this
     */
    public function exitAfterTrigger($condition)
    {
        $this->exitAfterTrigger = (bool) $condition;

        return $this;
    }

    public function getErrorSandbox()
    {
        if (is_null($this->sandbox)) {
            $this->sandbox = new ErrorSandbox($this->mode);
        }

        return $this->sandbox;
    }

    public function handleError($code, $message, $file, $line)
    {
        if (
            ($this->mode & $code)
            && ((error_reporting() & $code) || ($this->policy === static::POLICY_ALL))
        ) {
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
        $error = error_get_last();
        if (!$error || !($error['type'] & $this->mode)) {
            return;
        }
        if ($this->policy === static::POLICY_ALL || $this->isFatalError($error['type'])) {
            $this->onError(
                new ShutdownErrorException($error['message'], $error['type'], $error['file'], $error['line'])
            );
        }
    }

    public function pushListener(ListenerInterface $listener)
    {
        $this->listeners[] = $listener;

        return $this;
    }

    public function pushValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * @param \Exception|\Throwable $exception
     */
    private function onError($exception)
    {
        foreach ($this->validators as $validator) {
            try {
                $validator->onBeforeException($exception);
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