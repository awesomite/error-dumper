<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\StackTrace\StackTraceFactory;
use Awesomite\StackTrace\StackTraceFactoryInterface;
use Awesomite\StackTrace\StackTraceInterface;
use Awesomite\VarDumper\LightVarDumper;

class SerializableException implements SerializableExceptionInterface
{
    private $code;

    private $file;

    private $line;

    private $message;

    /**
     * @var null|StackTraceInterface
     */
    private $stackTrace = null;

    private $originalClass;

    /**
     * @var null|SerializableException
     */
    private $previousException = null;

    /**
     * @var bool
     */
    private $withContext;

    /**
     * @var null|array
     */
    private $context = null;

    /**
     * @var StackTraceFactoryInterface
     */
    private $stackTraceFactory;

    /**
     * @var ContextVarsFactoryInterface
     */
    private $contextVarsFactory;

    /**
     * @var \Exception|\Throwable
     */
    private $exception;

    /**
     * @param \Exception|\Throwable            $exception
     * @param int                              $stepLimit
     * @param bool                             $ignoreArgs
     * @param bool                             $withPrevious
     * @param bool                             $withContext
     * @param null|StackTraceFactoryInterface  $stackTraceFactory
     * @param null|ContextVarsFactoryInterface $contextVarsFactory
     */
    public function __construct(
        $exception,
        $stepLimit = 0,
        $ignoreArgs = false,
        $withPrevious = true,
        $withContext = true,
        StackTraceFactoryInterface $stackTraceFactory = null,
        ContextVarsFactoryInterface $contextVarsFactory = null
    ) {
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->message = $exception->getMessage();

        $varDumper = null;
        $stackTraceFactory = $stackTraceFactory ?: new StackTraceFactory($varDumper = new LightVarDumper(), 200);
        if ($withContext) {
            $contextVarsFactory = $contextVarsFactory ?: new ContextVarsFactory($varDumper ?: new LightVarDumper());
        }
        unset($varDumper);

        $this->stackTraceFactory = $stackTraceFactory;
        $this->contextVarsFactory = $contextVarsFactory;
        $this->originalClass = \get_class($exception);
        $this->withContext = $withContext;
        $this->exception = $exception;

        if ($withPrevious && $exception->getPrevious()) {
            $this->previousException = new static(
                $exception->getPrevious(),
                $stepLimit,
                $ignoreArgs,
                $withPrevious,
                false,
                $stackTraceFactory,
                null
            );
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
        return null === $this->stackTrace
            ? $this->stackTrace = $this->stackTraceFactory->createByThrowable($this->exception)
            : $this->stackTrace;
    }

    public function serialize()
    {
        return \serialize(array(
            'code'          => $this->code,
            'file'          => $this->file,
            'line'          => $this->line,
            'message'       => $this->message,
            'stackTrace'    => $this->getStackTrace(),
            'originalClass' => $this->originalClass,
            'previous'      => $this->previousException,
            'context'       => $this->getContext(),
        ));
    }

    public function unserialize($serialized)
    {
        $unserialized = \unserialize($serialized);
        $this->code = $unserialized['code'];
        $this->file = $unserialized['file'];
        $this->line = $unserialized['line'];
        $this->message = $unserialized['message'];
        $this->stackTrace = $unserialized['stackTrace'];
        $this->originalClass = $unserialized['originalClass'];
        $this->previousException = $unserialized['previous'];
        $this->context = $unserialized['context'];
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
        return !\is_null($this->previousException);
    }

    public function getContext()
    {
        if (null === $this->context) {
            $this->context = $this->withContext ? $this->contextVarsFactory->createContext() : array();
        }

        return $this->context;
    }
}
