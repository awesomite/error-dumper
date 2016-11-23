<?php

namespace Awesomite\ErrorDumper\StandardExceptions;

/**
 * @internal
 */
class FatalErrorException extends Exception
{
    /**
     * @internal
     *
     * @param int $code
     * @param string $message
     * @return $this
     */
    public function setCodeAndMessage($code, $message)
    {
        $humanCode = $this->errorNameToCode($code);
        if (!is_null($humanCode)) {
            $message = $humanCode . ' ' . $message;
        }

        return $this
            ->setCode($code)
            ->setMessage($message);
    }

    /**
     * @param int $code
     * @return string|null
     */
    private function errorNameToCode($code)
    {
        $mapping = $this->getAllConstants();

        return isset($mapping[$code]) ? $mapping[$code] : null;
    }

    /**
     * @see http://php.net/manual/en/errorfunc.constants.php
     * @return array
     */
    private function getAllConstants()
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

        $filtered = array_filter($all, function ($var) {
            return defined($var);
        });

        $result = array();
        foreach ($filtered as $name) {
            $result[constant($name)] = $name;
        }

        return $result;
    }
}