<?php

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\StackTrace\StackTraceFactory;

class SerializableException implements SerializableExceptionInterface
{
    private $code;

    private $file;

    private $line;

    private $message;

    private $stackTrace;

    private $originalClass;

    /**
     * @var SerializableException|null
     */
    private $previousException = null;

    /**
     * ClonedException constructor.
     * @param \Exception|\Throwable $exception
     * @param int $stepLimit
     * @param bool $ignoreArgs
     * @param bool $withPrevious
     */
    public function __construct($exception, $stepLimit = 0, $ignoreArgs = false, $withPrevious = true)
    {
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->message = $exception->getMessage();
        $stackTraceFactory = new StackTraceFactory();
        $this->stackTrace = $stackTraceFactory->createByThrowable($exception, $stepLimit, $ignoreArgs);
        $this->originalClass = get_class($exception);
        if ($withPrevious && $exception->getPrevious()) {
            $this->previousException = new static($exception->getPrevious());
        }
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    public function serialize()
    {
        return serialize(array(
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line,
            'message' => $this->message,
            'stackTrace' => $this->stackTrace,
            'originalClass' => $this->originalClass,
            'previous' => $this->previousException
        ));
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->code = $unserialized['code'];
        $this->file = $unserialized['file'];
        $this->line = $unserialized['line'];
        $this->message = $unserialized['message'];
        $this->stackTrace = $unserialized['stackTrace'];
        $this->originalClass = $unserialized['originalClass'];
        $this->previousException = $unserialized['previous'];
    }

    public function getOriginalClass()
    {
        return $this->originalClass;
    }

    public function getPrevious()
    {
        if (!$this->hasPrevious()) {
            throw new \LogicException('Previous exception does not exist!');
        }

        return $this->previousException;
    }

    public function hasPrevious()
    {
        return !is_null($this->previousException);
    }
}