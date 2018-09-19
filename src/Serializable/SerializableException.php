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
use Awesomite\VarDumper\LightVarDumper;

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
     * @var bool
     */
    private $withContext;

    /**
     * @var null|array
     */
    private $context = null;

    /**
     * @var null|ContextVarsFactoryInterface
     */
    private $contextFactory;

    /**
     * @param \Exception|\Throwable            $exception
     * @param int                              $stepLimit
     * @param bool                             $ignoreArgs
     * @param bool                             $withPrevious
     * @param bool                             $withContext
     * @param ContextVarsFactoryInterface|null $contextVariablesFactory
     */
    public function __construct(
        $exception,
        $stepLimit = 0,
        $ignoreArgs = false,
        $withPrevious = true,
        $withContext = true,
        ContextVarsFactoryInterface $contextVariablesFactory = null
    ) {
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->message = $exception->getMessage();
        $stackTraceFactory = new StackTraceFactory();
        $this->stackTrace = $stackTraceFactory->createByThrowable($exception, $stepLimit, $ignoreArgs);
        $this->originalClass = \get_class($exception);
        if ($withPrevious && $exception->getPrevious()) {
            $this->previousException = new static($exception->getPrevious());
        }
        $this->withContext = $withContext;
        $this->contextFactory = $contextVariablesFactory;
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
        return \serialize(array(
            'code'          => $this->code,
            'file'          => $this->file,
            'line'          => $this->line,
            'message'       => $this->message,
            'stackTrace'    => $this->stackTrace,
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
            $this->context = $this->withContext ? $this->getContextFactory()->createContext() : array();
        }

        return $this->context;
    }

    private function getContextFactory()
    {
        // TODO varDumper must be shared between EnvironmentVariablesFactory and stackTrace object
        return $this->contextFactory ?: $this->contextFactory = new ContextVarsFactory(new LightVarDumper());
    }
}
