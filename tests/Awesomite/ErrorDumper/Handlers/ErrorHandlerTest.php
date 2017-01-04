<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Listeners\ListenerClosure;
use Awesomite\ErrorDumper\Listeners\ValidatorClosure;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\TestBase;
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
class ErrorHandlerTest extends TestBase
{
    /**
     * @dataProvider providerInvalidConstructor
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConstructorMode($mode)
    {
        $reflection = new \ReflectionClass('Awesomite\\ErrorDumper\\Handlers\\ErrorHandler');
        $reflection->newInstanceArgs(func_get_args());
    }

    public function providerInvalidConstructor()
    {
        return array(
            array(function () {}),
            array('0'),
            array(false),
            array(E_ALL, false),
        );
    }

    public function testOnError()
    {
        $beeper = new Beeper();
        $handler = $this->createTestErrorHandler($beeper);
        $handler->registerOnError();

        $this->assertSame(0, $beeper->countBeeps());
        trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());
        restore_error_handler();
    }

    public function testGetErrorSandbox()
    {
        $errorHandler = new ErrorHandler();
        $sandbox = new ErrorSandbox();
        $this->assertInstanceOf(get_class($sandbox), $errorHandler->getErrorSandbox());
    }

    public function testValidatorAndListener()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper);
        $errorHandler->registerOnError();

        $this->assertSame(0, $beeper->countBeeps());
        trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());

        /** @var ValidatorClosure $validator */
        $validator = new ValidatorClosure(function () use (&$validator) {
            $validator->stopPropagation();
        });
        $errorHandler->pushValidator($validator);

        $this->assertSame(1, $beeper->countBeeps());
        trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());

        restore_error_handler();
    }

    public function testHandleException()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper);

        $this->assertSame(0, $beeper->countBeeps());
        $errorHandler->handleException(new \Exception('Test'));
        $this->assertSame(1, $beeper->countBeeps());
    }

    public function testHandleError()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper);

        $this->assertSame(0, $beeper->countBeeps());
        $errorHandler->handleError(E_ERROR, 'Test', __FILE__, __LINE__);
        $this->assertSame(1, $beeper->countBeeps());
    }

    public function testSkippedError()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper, E_ALL ^ E_DEPRECATED);

        $this->assertSame(0, $beeper->countBeeps());
        $errorHandler->handleError(E_DEPRECATED, 'test', __FILE__, __LINE__);
        $this->assertSame(0, $beeper->countBeeps());
    }

    public function testHandleShutdown()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper, null, ErrorHandler::POLICY_ALL);

        $this->assertSame(0, $beeper->countBeeps());
        $errorHandler->handleShutdown();
        $this->assertSame(0, $beeper->countBeeps());
        @trigger_error('Test');
        $errorHandler->handleShutdown();
        $this->assertSame(1, $beeper->countBeeps());
    }

    /**
     * @param Beeper $beeper
     * @param null|int $mode
     * @param int $policy
     * @return ErrorHandler
     */
    private function createTestErrorHandler(Beeper $beeper, $mode = null, $policy = ErrorHandler::POLICY_ERROR_REPORTING)
    {
        $result = new ErrorHandler($mode, $policy);
        $result->pushListener(new ListenerClosure(function () use ($beeper) {
            $beeper->beep();
        }));

        return $result;
    }
}