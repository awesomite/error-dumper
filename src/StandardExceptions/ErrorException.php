<?php

namespace Awesomite\ErrorDumper\StandardExceptions;

/**
 * @internal
 */
class ErrorException extends \ErrorException
{
    /**
     * ErrorException constructor.
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @param \Exception|null $previous
     */
    public function __construct($message, $code, $file, $line, $previous = null)
    {
        $humanCode = $this->errorNameToCode($code);
        parent::__construct(
            (!is_null($humanCode) ? $humanCode . ' ' : '') . $message,
            $code,
            $code,
            $file,
            $line,
            $previous
        );
    }

    /**
     * @param int $code
     * @return string|null
     */
    private function errorNameToCode($code)
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
            if (defined($name) && constant($name) === $code) {
                return $name;
            }
        }

        return null;
    }
}
