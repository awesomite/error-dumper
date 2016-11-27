<?php

namespace Awesomite\ErrorDumper\Sandboxes;

class ErrorSandbox implements ErrorSandboxInterface
{
    private $errorTypes;

    public function __construct($errorTypes = null)
    {
        $this->errorTypes = is_null($errorTypes) ? E_ALL | E_STRICT : $errorTypes;
    }

    public function executeSafely($callable)
    {
        $this->metaExecute($callable, function () {});
    }

    public function execute($callable)
    {
        $this->metaExecute($callable, function ($number, $str, $file, $line) {
            $exception = new SandboxException();
            $exception
                ->setCodeAndMessage($number, $str)
                ->setFile($file)
                ->setLine($line);
            throw $exception;
        });
    }

    private function metaExecute($callable, $errorCallback)
    {
        try {
            set_error_handler($errorCallback, $this->errorTypes);
            call_user_func($callable);
        } finally {
            restore_error_handler();
        }
    }
}