<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
