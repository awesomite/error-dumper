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
    public function testInvalidConstructor($mode)
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
        $this->assertSame($handler, $handler->registerOnError());

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
        $this->assertSame($errorHandler, $errorHandler->registerOnError());

        $this->assertSame(0, $beeper->countBeeps());
        trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());

        $validator = new ValidatorClosure(function () {
            ValidatorClosure::stopPropagation();
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

        $beeper->reset();
        $secondErrorHandler = $this->createTestErrorHandler($beeper, null, ErrorHandler::POLICY_ALL);
        $secondErrorHandler->handleError(E_NOTICE, 'E_NOTICE', __FILE__, __LINE__);
        $this->assertSame(1, $beeper->countBeeps());

        $beeper->reset();
        $thirdErrorHandler = $this->createTestErrorHandler($beeper, E_ALL ^ E_NOTICE, ErrorHandler::POLICY_ALL);
        $thirdErrorHandler->handleError(E_NOTICE, 'E_NOTICE', __FILE__, __LINE__);
        $this->assertSame(0, $beeper->countBeeps());
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

    public function testExitAfterTrigger()
    {
        $errorHandler = new ErrorHandler();
        foreach (array(false, true) as $condition) {
            $this->assertSame($errorHandler, $errorHandler->exitAfterTrigger($condition));
        }
    }

    public function testRegister()
    {
        $beeper = new Beeper();
        $handler = $this->createTestErrorHandler($beeper);
        $handler->register(ErrorHandler::TYPE_ERROR);
        $this->assertSame(0, $beeper->countBeeps());
        trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());
        restore_error_handler();
    }

    /**
     * @param Beeper $beeper
     * @param null|int $mode
     * @param int $policy
     * @return ErrorHandler
     */
    private function createTestErrorHandler(
        Beeper $beeper,
        $mode = null, 
        $policy = ErrorHandler::POLICY_ERROR_REPORTING
    ) {
        $result = new ErrorHandler($mode, $policy);
        $result->pushListener(new ListenerClosure(function () use ($beeper) {
            $beeper->beep();
        }));
        $result->exitAfterTrigger(false);

        return $result;
    }
}