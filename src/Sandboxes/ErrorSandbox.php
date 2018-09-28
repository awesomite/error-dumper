<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Sandboxes;

class ErrorSandbox implements ErrorSandboxInterface
{
    private $errorTypes;

    public function __construct($errorTypes = null)
    {
        $this->errorTypes = \is_null($errorTypes) ? \E_ALL | \E_STRICT : $errorTypes;
    }

    public function executeSafely($callable)
    {
        return $this->metaExecute($callable, function () {
        });
    }

    public function execute($callable)
    {
        return $this->metaExecute($callable, function ($number, $message, $file, $line) {
            throw new SandboxException($message, $number, $file, $line);
        });
    }

    private function metaExecute($callable, $errorCallback)
    {
        try {
            \set_error_handler($errorCallback, $this->errorTypes);
            $result = \call_user_func($callable);
            \restore_error_handler();

            return $result;
        } catch (\Exception $exception) {
            \restore_error_handler();

            throw $exception;
        } catch (\Throwable $exception) {
            \restore_error_handler();

            throw $exception;
        }
    }
}
