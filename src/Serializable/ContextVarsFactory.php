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

class ContextVarsFactory implements ContextVarsFactoryInterface
{
    /**
     * @var VarDumperInterface
     */
    private $varDumper;

    /**
     * @var array
     */
    private $exclude;

    /**
     * @param VarDumperInterface $varDumper
     * @param array              $exclude   e.g. array('_SERVER', '_ENV')
     */
    public function __construct(VarDumperInterface $varDumper, array $exclude = array())
    {
        $this->varDumper = $varDumper;
        $this->exclude = $exclude;
    }

    public function createContext()
    {
        $result = 'cli' === \php_sapi_name()
            ? $this->createForCli()
            : $this->createForHttp();

        $dateTime = new \DateTime();
        $result[] = $this->createFrom(
            'human-friendly time',
            \sprintf('%s %s', $dateTime->format('Y-m-d H:i:s T'), $dateTime->getTimezone()->getName())
        );
        $result[] = $this->createFrom('microtime(true)', \microtime(true));
        $result[] = $this->createFrom('PHP_VERSION', \PHP_VERSION);

        return $result;
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
        return $this->createFrom('$' . $name, isset($GLOBALS[$name]) ? $GLOBALS[$name] : null);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return ContextVar
     */
    private function createFrom($name, $value)
    {
        return new ContextVar($name, $this->varDumper->dumpAsString($value));
    }
}
