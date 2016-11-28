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
        $this->metaExecute($callable, function ($number, $message, $file, $line) {
            throw new SandboxException($message, $number, $file, $line);
        });
    }

    private function metaExecute($callable, $errorCallback)
    {
        try {
            set_error_handler($errorCallback, $this->errorTypes);
            call_user_func($callable);
        } catch (\Throwable $exception) {
            restore_error_handler();
            throw $exception;
        } catch (\Exception $exception) {
            restore_error_handler();
            throw $exception;
        }

        restore_error_handler();
    }
}