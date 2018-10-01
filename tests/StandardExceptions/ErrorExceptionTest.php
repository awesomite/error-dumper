<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\StandardExceptions;

use Awesomite\ErrorDumper\AbstractTestCase;

/**
 * @internal
 */
final class ErrorExceptionTest extends AbstractTestCase
{
    /**
     * @dataProvider providerConstructor
     *
     * @param string      $message
     * @param int         $code
     * @param int         $severity
     * @param null|string $humanCode
     * @param string      $file
     * @param int         $line
     */
    public function testConstructor($message, $code, $severity, $humanCode, $file, $line)
    {
        $exception = $this->createErrorException($message, $code, $severity, $file, $line);
        $this->assertInstanceOf('Awesomite\ErrorDumper\StandardExceptions\ErrorException', $exception);
        $expectedMessage = !\is_null($humanCode) ? $humanCode . ' ' . $message : $message;
        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($severity, $exception->getSeverity());
        $this->assertSame($file, $exception->getFile());
        $this->assertSame($line, $exception->getLine());
    }

    public function providerConstructor()
    {
        return array(
            array('First exception', 0, \E_USER_NOTICE, 'E_USER_NOTICE', __FILE__, __LINE__),
            array('Second exception', 1, \E_WARNING, 'E_WARNING', __FILE__, __LINE__),
            array('Third exception', 2, 0, null, __FILE__, __LINE__),
        );
    }

    /**
     * @dataProvider providerIsDeprecated
     *
     * @param int  $severity
     * @param bool $result
     */
    public function testIsDeprecated($severity, $result)
    {
        $error = $this->createErrorException('', 0, $severity, __FILE__, __LINE__);
        $this->assertSame($result, $error->isDeprecated());
    }

    public function providerIsDeprecated()
    {
        return array(
            array(\E_DEPRECATED, true),
            array(\E_USER_DEPRECATED, true),
            array(\E_ERROR, false),
            array(\E_USER_ERROR, false),
        );
    }

    /**
     * @dataProvider providerIsNotice
     *
     * @param int  $severity
     * @param bool $result
     */
    public function testIsNotice($severity, $result)
    {
        $error = $this->createErrorException('', 0, $severity, __FILE__, __LINE__);
        $this->assertSame($result, $error->isNotice());
    }

    public function providerIsNotice()
    {
        return array(
            array(\E_NOTICE, true),
            array(\E_USER_NOTICE, true),
            array(\E_ERROR, false),
            array(\E_USER_ERROR, false),
        );
    }

    /**
     * @dataProvider providerIsSeverity
     *
     * @param int  $severity
     * @param int  $compareTo
     * @param bool $result
     */
    public function testIsSeverity($severity, $compareTo, $result)
    {
        $error = $this->createErrorException('', 0, $severity, __FILE__, __LINE__);
        $this->assertSame($result, $error->isSeverity($compareTo));
    }

    public function providerIsSeverity()
    {
        return array(
            array(\E_WARNING, \E_ALL, true),
            array(\E_ALL & ~\E_DEPRECATED, \E_DEPRECATED, false),
            array(\E_USER_NOTICE, \E_NOTICE, false),
        );
    }

    /**
     * @dataProvider providerErrorNameToCode
     *
     * @param int    $code
     * @param string $name
     */
    public function testErrorNameToCode($code, $name)
    {
        $exception = new ErrorException('Test', 0, \E_DEPRECATED, __FILE__, __LINE__);
        $reflectionMethod = new \ReflectionMethod($exception, 'errorNameToCode');
        $reflectionMethod->setAccessible(true);
        $this->assertSame($name, $reflectionMethod->invoke($exception, $code));
    }

    public function providerErrorNameToCode()
    {
        return array(
            array(\E_DEPRECATED, 'E_DEPRECATED'),
            array(\E_WARNING, 'E_WARNING'),
            array(\E_COMPILE_ERROR, 'E_COMPILE_ERROR'),
        );
    }

    /**
     * @param string $message
     * @param int    $code
     * @param int    $severity
     * @param string $file
     * @param int    $line
     *
     * @return ErrorException
     */
    private function createErrorException($message, $code, $severity, $file, $line)
    {
        return new ErrorException($message, $code, $severity, $file, $line);
    }
}
