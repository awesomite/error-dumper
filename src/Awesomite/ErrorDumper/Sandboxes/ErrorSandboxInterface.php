<?php

namespace Awesomite\ErrorDumper\Sandboxes;

interface ErrorSandboxInterface
{
    /**
     * @param callable $callback
     */
    public function executeSafely($callback);

    /**
     * Converts error into exception in case of occurrence
     *
     * @param callable $callback
     *
     * @throws SandboxException
     */
    public function execute($callback);
}