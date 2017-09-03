<?php

namespace Awesomite\ErrorDumper\Sandboxes;

interface ErrorSandboxInterface
{
    /**
     * @param callable $callable
     *
     * @return mixed Returns the return value of the callback
     */
    public function executeSafely($callable);

    /**
     * Converts error into exception in case of occurrence
     *
     * @param callable $callable
     *
     * @return mixed Returns the return value of the callback
     *
     * @throws SandboxException
     */
    public function execute($callable);
}
