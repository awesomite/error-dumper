<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class ErrorDumperTest extends TestBase
{
    public function testCreateDevHandler()
    {
        $previousExceptionHandler = $this->getExceptionHandler();
        $previousErrorHandler = $this->getErrorHandler();

        $errorDumper = new ErrorDumper();
        $this->assertInstanceOf(
            '\Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface',
            $errorDumper->createDevHandler()
        );

        $currentExceptionHandler = $this->getExceptionHandler();
        $currentErrorHandler = $this->getErrorHandler();

        $this->assertSame($previousExceptionHandler, $currentExceptionHandler, 'Exception handler is changed!');
        $this->assertSame($previousErrorHandler, $currentErrorHandler, 'Error handler is changed!');
    }

    /**
     * @return callable|null
     */
    private function getExceptionHandler()
    {
        $result = set_exception_handler(function () {});
        restore_exception_handler();

        return $result;
    }

    /**
     * @return callable|null
     */
    private function getErrorHandler()
    {
        $result = set_error_handler(function () {});
        restore_error_handler();

        return $result;
    }
}