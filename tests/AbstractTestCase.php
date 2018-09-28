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
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    private $errorHandler;

    private $exceptionHandler;

    protected function setUp()
    {
        parent::setUp();
        $this->expectOutputString('');
        $this->errorHandler = $this->getCurrentErrorHandler();
        $this->exceptionHandler = $this->getCurrentExceptionHandler();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->assertSame(
            $this->errorHandler,
            $this->getCurrentErrorHandler(),
            'Error handler has changed'
        );
        $this->assertSame(
            $this->exceptionHandler,
            $this->getCurrentExceptionHandler(),
            'Exception handler has changed'
        );
    }

    private function getCurrentErrorHandler()
    {
        $current = \set_error_handler(function () {
        });
        \restore_error_handler();

        return $current;
    }

    private function getCurrentExceptionHandler()
    {
        $current = \set_exception_handler(function () {
        });
        \restore_exception_handler();

        return $current;
    }
}
