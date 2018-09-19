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

final class ContextVarsFactory implements ContextVarsFactoryInterface
{
    /**
     * @var VarDumperInterface
     */
    private $varDumper;

    /**
     * @var array
     */
    private $exclude;

    public function __construct(VarDumperInterface $varDumper, array $exclude = array())
    {
        $this->varDumper = $varDumper;
        $this->exclude = $exclude;
    }

    public function createContext()
    {
        return 'cli' === \php_sapi_name()
            ? $this->createForCli()
            : $this->createForHttp();
    }

    private function createForCli()
    {
        return $this->createFromGlobals(array(
             'argv',
             'argc',
             '_SERVER',
             '_ENV',
         ));
    }

    private function createForHttp()
    {
        return $this->createFromGlobals(array(
            '_SERVER',
            '_GET',
            '_POST',
            '_FILES',
            '_COOKIE',
            '_SESSION',
            '_ENV',
        ));
    }

    private function createFromGlobals($names)
    {
        $result = array();
        foreach ($names as $name) {
            if (\in_array($name, $this->exclude, true)) {
                continue;
            }
            $result[] = $this->createFromGlobal($name);
        }

        return $result;
    }

    private function createFromGlobal($name)
    {
        return $this->createFrom($name, isset($GLOBALS[$name]) ? $GLOBALS[$name] : null);
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
