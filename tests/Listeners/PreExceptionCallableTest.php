<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\TestBase;
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
class PreExceptionCallableTest extends TestBase
{
    public function testTrigger()
    {
        $beeper = new Beeper();
        $validator = new PreExceptionCallable(function () use ($beeper) {
            $beeper->beep();
        });

        $this->assertSame(0, $beeper->countBeeps());
        $validator->preException(new \Exception());
        $this->assertSame(1, $beeper->countBeeps());
    }

    /**
     * @expectedException \Awesomite\ErrorDumper\Listeners\StopPropagationException
     */
    public function testStopPropagation()
    {
        PreExceptionCallable::stopPropagation();
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
        new PreExceptionCallable($notCallable);
    }

    public function providerInvalidConstructor()
    {
        return array(
            array(1),
            array(false),
            array(new \stdClass()),
        );
    }
}