<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\TestBase;

/**
 * @internal
 */
class CallableReflectionTest extends TestBase
{
    /**
     * @dataProvider providerGetReflection
     *
     * @param callable $callable
     */
    public function testGetReflection($callable)
    {
        $reflection = new CallableReflection($callable);
        $method = new \ReflectionMethod(get_class($reflection), 'getReflection');
        $method->setAccessible(true);
        $result = $method->invoke($reflection, $callable);
        $this->assertInstanceOf('ReflectionFunctionAbstract', $result);
    }

    public function providerGetReflection()
    {
        return array(
            array(function () {
            }),
            array('strpos'),
            array(new TestInvokableObject()),
            array('Awesomite\ErrorDumper\Listeners\TestInvokableObject::doStaticAction'),
            array(array(new TestInvokableObject(), 'doSomething')),
            array(array(new TestInvokableObject(), 'doStaticAction')),
        );
    }

    /**
     * @dataProvider providerTestIsThrowableCallable
     *
     * @param callable $callable
     * @param bool $expected
     */
    public function testIsThrowableCallable($callable, $expected)
    {
        $reflecton = new CallableReflection($callable);
        for ($i = 0; $i < 2; $i++) {
            $this->assertSame($expected, $reflecton->isThrowableCallable());
        }
    }

    public function providerTestIsThrowableCallable()
    {
        return array(
            array(new TestInvokableObject(), true),
            array('Awesomite\ErrorDumper\Listeners\TestInvokableObject::handleStdClass', false),
        );
    }
}
