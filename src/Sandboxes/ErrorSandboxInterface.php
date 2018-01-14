<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
