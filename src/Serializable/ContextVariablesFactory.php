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

/**
 * @internal
 */
class ContextVariablesFactory
{
    public static function create(VarDumperInterface $varDumper)
    {
        return 'cli' === \php_sapi_name()
            ? static::createForCli($varDumper)
            : static::createForHttp($varDumper);
    }

    /**
     * @param VarDumperInterface $varDumper
     *
     * @return array
     */
    public static function createForCli(VarDumperInterface $varDumper)
    {
        global $argv;

        return array(
            static::createFrom($varDumper, 'argv', isset($argv) ? $argv : null),
            static::createFrom($varDumper, '_SERVER', isset($_SERVER) ? $_SERVER : null),
            static::createFrom($varDumper, '_ENV', isset($_ENV) ? $_ENV : null),
        );
    }

    /**
     * @param VarDumperInterface $varDumper
     *
     * @return VariableInterface[]
     */
    public static function createForHttp(VarDumperInterface $varDumper)
    {
        return array(
            static::createFrom($varDumper, '_SERVER', isset($_SERVER) ? $_SERVER : null),
            static::createFrom($varDumper, '_GET', isset($_GET) ? $_GET : null),
            static::createFrom($varDumper, '_POST', isset($_POST) ? $_POST : null),
            static::createFrom($varDumper, '_FILES', isset($_FILES) ? $_FILES : null),
            static::createFrom($varDumper, '_COOKIE', isset($_COOKIE) ? $_COOKIE : null),
            static::createFrom($varDumper, '_SESSION', isset($_SESSION) ? $_SESSION : null),
            static::createFrom($varDumper, '_ENV', isset($_ENV) ? $_ENV : null),
        );
    }

    /**
     * @param VarDumperInterface $varDumper
     * @param string             $name
     * @param mixed              $value
     *
     * @return Variable
     */
    private static function createFrom(VarDumperInterface $varDumper, $name, $value)
    {
        return new Variable($name, $varDumper->dumpAsString($value));
    }
}
