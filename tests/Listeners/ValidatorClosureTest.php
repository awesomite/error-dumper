<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\TestBase;
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
class ValidatorClosureTest extends TestBase
{
    public function testTrigger()
    {
        $beeper = new Beeper();
        $validator = new ValidatorClosure(function () use ($beeper) {
            $beeper->beep();
        });

        $this->assertSame(0, $beeper->countBeeps());
        $validator->onBeforeException(new \Exception());
        $this->assertSame(1, $beeper->countBeeps());
    }

    /**
     * @expectedException \Awesomite\ErrorDumper\Listeners\StopPropagationException
     */
    public function testStopPropagation()
    {
        ValidatorClosure::stopPropagation();
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
        new ValidatorClosure($notCallable);
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