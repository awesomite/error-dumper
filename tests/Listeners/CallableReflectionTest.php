<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\AbstractTestCase;

/**
 * @internal
 */
final class CallableReflectionTest extends AbstractTestCase
{
    /**
     * @dataProvider providerGetReflection
     *
     * @param callable $callable
     */
    public function testGetReflection($callable)
    {
        $reflection = new CallableReflection($callable);
        $method = new \ReflectionMethod(\get_class($reflection), 'getReflection');
        $method->setAccessible(true);
        $result = $method->invoke($reflection, $callable);
        $this->assertInstanceOf('ReflectionFunctionAbstract', $result);
    }

    public function providerGetReflection()
    {
        return array(
            array(
                function () {
                },
            ),
            array('strpos'),
            array(new TestInvokableObject()),
            array('Awesomite\ErrorDumper\Listeners\TestInvokableObject::doStaticAction'),
            array(array(new TestInvokableObject(), 'doSomething')),
            array(array(new TestInvokableObject(), 'doStaticAction')),
        );
    }
}
