<?php

namespace Awesomite\ErrorDumper\Handlers;

use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;

/**
 * @internal
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        new ErrorHandler(true);
    }

    public function testOnError()
    {
        $triggered = false;
        $event = function () use (&$triggered) {
            $triggered = true;
        };
        $handler = new ErrorHandler($event);
        $handler->registerOnError();
        trigger_error('Test');
        $this->assertTrue($triggered);
        restore_error_handler();
    }


    public function testWithoutErrorSandbox()
    {
        $errorHandler = new ErrorHandler(function () {});
        $this->checkGetErrorSandbox($errorHandler, false);
    }

    public function testGetErrorSandbox()
    {
        $errorHandler = new ErrorHandler(function () {});
        $errorHandler->registerOnError();
        $this->checkGetErrorSandbox($errorHandler, true);
        restore_error_handler();
    }

    private function checkGetErrorSandbox(ErrorHandler $errorHandler, $hasSandbox)
    {
        if (!$hasSandbox) {
            $exception = new \LogicException();
            $this->setExpectedException(get_class($exception));
        }
        $sandbox = new ErrorSandbox();
        $this->assertInstanceOf(get_class($sandbox), $errorHandler->getErrorSandbox());
    }
}