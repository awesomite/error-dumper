<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\TestBase;
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
class ListenerClosureTest extends TestBase
{
    public function testTrigger()
    {
        $beeper = new Beeper();
        $listener = new ListenerClosure(function () use ($beeper) {
            $beeper->beep();
        });

        $this->assertSame(0, $beeper->countBeeps());
        $listener->onException(new \Exception());
        $this->assertSame(1, $beeper->countBeeps());
    }

    /**
     * @dataProvider providerInvalidConstructor
     *
     * @expectedException \InvalidArgumentException
     *
     * @param $notCallable
     */
    public function testInvalidConstructor($notCallable)
    {
        new ListenerClosure($notCallable);
    }

    public function providerInvalidConstructor()
    {
        return array(
            array(1),
            array(false),
            array('1'),
            array(new \stdClass()),
        );
    }
}