<?php

namespace Awesomite\ErrorDumper\StandardExceptions;

use Awesomite\ErrorDumper\TestBase;

/**
 * @internal
 */
class ErrorExceptionTest extends TestBase
{
    /**
     * @dataProvider providerConstructor
     *
     * @param string      $message
     * @param int         $code
     * @param string|null $humanCode
     * @param string      $file
     * @param int         $line
     */
    public function testConstructor($message, $code, $humanCode, $file, $line)
    {
        $exception = $this->createErrorException($message, $code, $file, $line);
        $this->assertInstanceOf('Awesomite\ErrorDumper\StandardExceptions\ErrorException', $exception);
        $expectedMessage = !is_null($humanCode) ? $humanCode . ' ' . $message : $message;
        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($file, $exception->getFile());
        $this->assertSame($line, $exception->getLine());
    }

    public function providerConstructor()
    {
        return array(
            array('First exception', E_USER_NOTICE, 'E_USER_NOTICE', __FILE__, __LINE__),
            array('Second exception', E_WARNING, 'E_WARNING', __FILE__, __LINE__),
            array('Third exception', 0, null, __FILE__, __LINE__),
        );
    }

    /**
     * @param string $message
     * @param int    $code
     * @param string $file
     * @param int    $line
     *
     * @return ErrorException
     */
    private function createErrorException($message, $code, $file, $line)
    {
        return new ErrorException($message, $code, $file, $line);
    }
}
