<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\VarDumper\VarDumperInterface;

final class ContextVariablesFactory implements ContextVariablesFactoryInterface
{
    /**
     * @var VarDumperInterface
     */
    private $varDumper;

    public function __construct(VarDumperInterface $varDumper)
    {
        $this->varDumper = $varDumper;
    }

    public function createContext()
    {
        return 'cli' === \php_sapi_name()
            ? $this->createForCli()
            : $this->createForHttp();
    }

    private function createForCli()
    {
        global $argv;

        return array(
            $this->createFrom('argv', isset($argv) ? $argv : null),
            $this->createFrom('_SERVER', isset($_SERVER) ? $_SERVER : null),
            $this->createFrom('_ENV', isset($_ENV) ? $_ENV : null),
        );
    }

    private function createForHttp()
    {
        return array(
            $this->createFrom('_SERVER', isset($_SERVER) ? $_SERVER : null),
            $this->createFrom('_GET', isset($_GET) ? $_GET : null),
            $this->createFrom('_POST', isset($_POST) ? $_POST : null),
            $this->createFrom('_FILES', isset($_FILES) ? $_FILES : null),
            $this->createFrom('_COOKIE', isset($_COOKIE) ? $_COOKIE : null),
            $this->createFrom('_SESSION', isset($_SESSION) ? $_SESSION : null),
            $this->createFrom('_ENV', isset($_ENV) ? $_ENV : null),
        );
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return Variable
     */
    private function createFrom($name, $value)
    {
        return new Variable($name, $this->varDumper->dumpAsString($value));
    }
}
