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

use Awesomite\ErrorDumper\AbstractTestCase;
use Awesomite\VarDumper\LightVarDumper;

/**
 * @internal
 */
final class ContextVarFactoryTest extends AbstractTestCase
{
    public function testCreate()
    {
        $factory = new ContextVarsFactory(new LightVarDumper());
        $vars = $factory->createContext();
        $this->assertContextVars($vars);
    }

    public function testCreateForCli()
    {
        $factory = new ContextVarsFactory(new LightVarDumper());
        $vars = $this->callPrivateMethod($factory, 'createForCli');
        $this->assertContextVars($vars);
    }

    public function testCreateForHttp()
    {
        $factory = new ContextVarsFactory(new LightVarDumper());
        $vars = $this->callPrivateMethod($factory, 'createForHttp');
        $this->assertContextVars($vars);
    }

    public function testExclude()
    {
        $factoryWithArgv = new ContextVarsFactory(new LightVarDumper());
        $namesWithArgv = $this->convertVarsToNames($this->callPrivateMethod($factoryWithArgv, 'createForCli'));
        $this->assertContains('argv', $namesWithArgv);

        $factoryWithoutArgv = new ContextVarsFactory(new LightVarDumper(), array('argv'));
        $namesWithoutArgv = $this->convertVarsToNames($this->callPrivateMethod($factoryWithoutArgv, 'createForCli'));
        $this->assertNotContains('argv', $namesWithoutArgv);
    }

    /**
     * @param ContextVarInterface[] $vars
     *
     * @return string[]
     */
    private function convertVarsToNames($vars)
    {
        return \array_map(
            function (ContextVarInterface $var) {
                return \ltrim($var->getName(), '$');
            },
            $vars
        );
    }

    private function assertContextVars($vars)
    {
        $this->assertInternalType('array', $vars);
        $this->assertGreaterThan(0, \count($vars));
        foreach ($vars as $var) {
            $this->assertInstanceOf('Awesomite\ErrorDumper\Serializable\ContextVarInterface', $var);
        }
    }

    private function callPrivateMethod($object, $method, $args = array())
    {
        $reflectionMethod = new \ReflectionMethod($object, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
    }
}
