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
        $this->assertInternalType('array', $vars);
        $this->assertGreaterThan(0, \count($vars));
        foreach ($vars as $var) {
            $this->assertInstanceOf('Awesomite\ErrorDumper\Serializable\ContextVarInterface', $var);
        }
    }
}
