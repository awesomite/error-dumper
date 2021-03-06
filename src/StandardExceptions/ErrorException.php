<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\StandardExceptions;

class ErrorException extends \ErrorException
{
    /**
     * @param string                     $message
     * @param int                        $code
     * @param int                        $severity
     * @param string                     $file
     * @param int                        $line
     * @param null|\Exception|\Throwable $previous
     */
    public function __construct($message, $code, $severity, $file, $line, $previous = null)
    {
        $humanCode = $this->errorNameToCode($severity);
        parent::__construct(
            (!\is_null($humanCode) ? $humanCode . ' ' : '') . $message,
            $code,
            $severity,
            $file,
            $line,
            $previous
        );
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->isSeverity(\E_DEPRECATED | \E_USER_DEPRECATED);
    }

    /**
     * @return bool
     */
    public function isNotice()
    {
        return $this->isSeverity(\E_NOTICE | \E_USER_NOTICE);
    }

    /**
     * @param int $severity bitmask e.g. E_DEPRECATED | E_USER_DEPRECATED
     *
     * @return bool
     *
     * @see http://php.net/manual/en/errorfunc.constants.php
     */
    public function isSeverity($severity)
    {
        return (bool)($severity & $this->getSeverity());
    }

    /**
     * @param int $severity
     *
     * @return null|string
     *
     * @see http://php.net/manual/en/errorfunc.constants.php
     */
    private function errorNameToCode($severity)
    {
        $all = array(
            'E_ERROR',
            'E_WARNING',
            'E_PARSE',
            'E_NOTICE',
            'E_CORE_ERROR',
            'E_CORE_WARNING',
            'E_COMPILE_ERROR',
            'E_COMPILE_WARNING',
            'E_USER_ERROR',
            'E_USER_WARNING',
            'E_USER_NOTICE',
            'E_RECOVERABLE_ERROR',
            'E_DEPRECATED',
            'E_USER_DEPRECATED',
        );

        foreach ($all as $name) {
            if (\defined($name) && \constant($name) === $severity) {
                return $name;
            }
        }

        return null;
    }
}
