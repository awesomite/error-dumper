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
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
final class OnExceptionCallableTest extends AbstractTestCase
{
    public function testTrigger()
    {
        $beeper = new Beeper();
        $listener = new OnExceptionCallable(function () use ($beeper) {
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
        new OnExceptionCallable($notCallable);
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
