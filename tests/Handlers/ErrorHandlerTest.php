<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\AbstractTestCase;
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;
use Awesomite\ErrorDumper\Listeners\PreExceptionCallable;
use Awesomite\ErrorDumper\Listeners\StopPropagationException;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\TestHelpers\Beeper;

/**
 * @internal
 */
final class ErrorHandlerTest extends AbstractTestCase
{
    /**
     * @dataProvider providerInvalidConstructor
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConstructor($mode)
    {
        $reflection = new \ReflectionClass('Awesomite\\ErrorDumper\\Handlers\\ErrorHandler');
        $reflection->newInstanceArgs(\func_get_args());
    }

    public function providerInvalidConstructor()
    {
        return array(
            array(
                function () {
                },
            ),
            array('0'),
            array(false),
            array(new \stdClass()),
        );
    }

    public function testOnError()
    {
        $beeper = new Beeper();
        $handler = $this->createTestErrorHandler($beeper);
        $this->assertSame($handler, $handler->registerOnError());

        $this->assertSame(0, $beeper->countBeeps());
        \trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());
        \restore_error_handler();
    }

    public function testGetErrorSandbox()
    {
        $errorHandler = new ErrorHandler();
        $sandbox = new ErrorSandbox();
        $this->assertInstanceOf(\get_class($sandbox), $errorHandler->getErrorSandbox());
    }

    public function testValidatorAndListener()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper);
        $this->assertSame($errorHandler, $errorHandler->registerOnError());

        $this->assertSame(0, $beeper->countBeeps());
        \trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());

        $validator = new PreExceptionCallable(function () {
            throw new StopPropagationException();
        });
        $errorHandler->pushPreListener($validator);

        $this->assertSame(1, $beeper->countBeeps());
        \trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());

        \restore_error_handler();
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
        $errorHandler->handleError(\E_ERROR, 'Test', __FILE__, __LINE__);
        $this->assertSame(1, $beeper->countBeeps());

        $beeper->reset();
        $secondErrorHandler = $this->createTestErrorHandler($beeper, null);
        $secondErrorHandler->handleError(\E_NOTICE, 'E_NOTICE', __FILE__, __LINE__);
        $this->assertSame(1, $beeper->countBeeps());

        $beeper->reset();
        $thirdErrorHandler = $this->createTestErrorHandler($beeper, \E_ALL ^ \E_NOTICE);
        $thirdErrorHandler->handleError(\E_NOTICE, 'E_NOTICE', __FILE__, __LINE__);
        $this->assertSame(0, $beeper->countBeeps());
    }

    public function testSkippedError()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper, \E_ALL ^ \E_DEPRECATED);

        $this->assertSame(0, $beeper->countBeeps());
        $errorHandler->handleError(\E_DEPRECATED, 'test', __FILE__, __LINE__);
        $this->assertSame(0, $beeper->countBeeps());
    }

    public function testHandleShutdown()
    {
        $beeper = new Beeper();
        $errorHandler = $this->createTestErrorHandler($beeper, null);
        $this->assertSame(0, $beeper->countBeeps());
        $errorType = \E_USER_NOTICE;

        $errorHandler->handleShutdown();
        $this->assertSame(0, $beeper->countBeeps());
        @\trigger_error('Test', $errorType);
        $errorHandler->handleShutdown();
        $this->assertSame(0, $beeper->countBeeps());

        $reflectionProperty = new \ReflectionProperty(\get_class($errorHandler), 'fatalErrors');
        $reflectionProperty->setAccessible(true);
        $originalFatalErrors = $reflectionProperty->getValue();
        $reflectionProperty->setValue($errorHandler, array($errorType));

        @\trigger_error('Test', $errorType);
        $errorHandler->handleShutdown();
        $this->assertSame(1, $beeper->countBeeps());

        $reflectionProperty->setValue($errorHandler, $originalFatalErrors);
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
        \trigger_error('Test');
        $this->assertSame(1, $beeper->countBeeps());
        \restore_error_handler();
    }

    protected function setUp()
    {
        parent::setUp();
        if (\function_exists('error_clear_last')) {
            while (\error_get_last()) {
                \error_clear_last();
            }
        }
    }

    /**
     * @param Beeper   $beeper
     * @param null|int $mode
     * @param int      $policy
     *
     * @return ErrorHandler
     */
    private function createTestErrorHandler(Beeper $beeper, $mode = null)
    {
        $result = new ErrorHandler($mode);
        $result->pushListener(new OnExceptionCallable(function () use ($beeper) {
            $beeper->beep();
        }));
        $result->exitAfterTrigger(false);

        return $result;
    }
}
