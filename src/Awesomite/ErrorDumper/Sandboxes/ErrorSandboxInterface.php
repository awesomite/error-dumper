<?php

namespace Awesomite\ErrorDumper\Sandboxes;

interface ErrorSandboxInterface
{
    /**
     * @param callable $callable
     */
    public function executeSafely($callable);

    /**
     * Converts error into exception in case of occurrence
     *
     * @param callable $callable
     *
     * @throws SandboxException
     */
    public function execute($callable);
}