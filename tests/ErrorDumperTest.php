<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
final class ErrorDumperTest extends AbstractTestCase
{
    public function testCreateDevHandler()
    {
        $prevExceptionHandler = $this->getExceptionHandler();
        $prevErrorHandler = $this->getErrorHandler();

        $this->assertInstanceOf(
            '\Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface',
            ErrorDumper::createDevHandler()
        );

        $currExceptionHandler = $this->getExceptionHandler();
        $currErrorHandler = $this->getErrorHandler();

        $this->assertSame($prevExceptionHandler, $currExceptionHandler, 'Exception handler is changed!');
        $this->assertSame($prevErrorHandler, $currErrorHandler, 'Error handler is changed!');
    }

    /**
     * @return callable|null
     */
    private function getExceptionHandler()
    {
        $result = \set_exception_handler(function () {
        });
        \restore_exception_handler();

        return $result;
    }

    /**
     * @return callable|null
     */
    private function getErrorHandler()
    {
        $result = \set_error_handler(function () {
        });
        \restore_error_handler();

        return $result;
    }
}
