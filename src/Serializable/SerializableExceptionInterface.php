<?php

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\StackTrace\StackTraceInterface;

interface SerializableExceptionInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return int
     */
    public function getCode();

    /**
     * @return string
     */
    public function getFile();

    /**
     * @return int
     */
    public function getLine();

    /**
     * @return StackTraceInterface
     */
    public function getStackTrace();

    /**
     * @return string
     */
    public function getOriginalClass();

    /**
     * @return bool
     */
    public function hasPrevious();

    /**
     * @return SerializableExceptionInterface
     */
    public function getPrevious();
}
