<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\TestBase;

class CallableValidatorTest extends TestBase
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

    /**
     * @dataProvider providerInvalidParameters
     *
     * @param $callable
     */
    public function testInvalidParameters($callable)
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid callable, first argument must be Throwable');
        new OnExceptionCallable($callable);
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
            array(get_class($this) . '::invalidCallableListener2'),
            array(array(get_class($this), 'invalidCallableListener2')),
        );
    }

    public function invalidCallableListener(array $exception)
    {
    }

    public static function invalidCallableListener2(array $exception)
    {
    }
}