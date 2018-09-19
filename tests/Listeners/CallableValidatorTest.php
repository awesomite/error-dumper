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

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\AbstractTestCase;

final class CallableValidatorTest extends AbstractTestCase
{
    public function testValidator()
    {
        $string = new AppendableString();
        $errorHandler = new ErrorHandler();
        $errorHandler->exitAfterTrigger(false);

        $errorHandler->pushPreListener(new PreExceptionCallable(function (\InvalidArgumentException $e) use ($string) {
            $string->append('Prelistener: ' . $e->getMessage());
        }));

        $errorHandler->pushListener(new OnExceptionCallable(function (\DomainException $e) use ($string) {
            $string->append('Listener: ' . $e->getMessage());
        }));

        $errorHandler->handleException(new \InvalidArgumentException('Hello'));
        $errorHandler->handleException(new \DomainException('World'));

        $expected
            = <<<'EXCPECTED'
Prelistener: Hello
Listener: World
EXCPECTED;

        $this->assertSame($expected, (string)$string);
    }

    public function providerInvalidParameters()
    {
        return array(
            array('strpos'),
            array(array($this, 'invalidCallableListener')),
            array(
                function (\stdClass $exception) {
                },
            ),
            array(\get_class($this) . '::invalidCallableListener2'),
            array(array(\get_class($this), 'invalidCallableListener2')),
        );
    }

    public function invalidCallableListener(array $exception)
    {
    }

    public static function invalidCallableListener2(array $exception)
    {
    }
}
