<?php

namespace Awesomite\ErrorDumper\Sandboxes;

class ErrorSandbox implements ErrorSandboxInterface
{
    private $errorTypes;

    public function __construct($errorTypes = null)
    {
        $this->errorTypes = is_null($errorTypes) ? E_ALL | E_STRICT : $errorTypes;
    }

    public function executeSafely($callback)
    {
        $this->metaExecute($callback);
    }

    public function execute($callback)
    {
        if ($exception = $this->metaExecute($callback)) {
            throw $exception;
        }
    }

    private function metaExecute($callback)
    {
        $exception = null;
        $errorCallback = function ($number, $str, $file, $line) use (&$exception) {
            $exception = new SandboxException();
            $exception
                ->setCodeAndMessage($number, $str)
                ->setFile($file)
                ->setLine($line);
        };
        $previous = set_error_handler($errorCallback, $this->errorTypes);
        call_user_func($callback);
        if (!is_null($previous)) {
            restore_error_handler();
        }

        return $exception;
    }
}