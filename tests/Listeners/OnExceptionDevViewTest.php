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

use Awesomite\ErrorDumper\TestBase;
use Awesomite\ErrorDumper\TestHelpers\Beeper;
use Awesomite\ErrorDumper\Views\ViewInterface;

/**
 * @internal
 */
class OnExceptionDevViewTest extends TestBase
{
    public function testTrigger()
    {
        $beeper = new Beeper();
        $listener = new OnExceptionDevView($this->createView($beeper));
        $this->assertSame(0, $beeper->countBeeps());
        $listener->onException(new \Exception());
        $this->assertSame(1, $beeper->countBeeps());
    }

    /**
     * @param Beeper $beeper
     *
     * @return ViewInterface
     */
    private function createView(Beeper $beeper)
    {
        $mock = $this->getMock('Awesomite\\ErrorDumper\\Views\\ViewInterface');
        $mock->method('display')->willReturnCallback(function () use ($beeper) {
            $beeper->beep();
        });

        return $mock;
    }
}
