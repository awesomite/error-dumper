<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class AbstractErrorDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerGetErrorHandler
     *
     * @param AbstractErrorDumper $errorDumper
     */
    public function testGetErrorHandler(AbstractErrorDumper $errorDumper)
    {
        $this->assertInstanceOf(
            'Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface',
            $errorDumper->getErrorHandler()
        );
    }

    public function providerGetErrorHandler()
    {
        return array(
            array(new DevErrorDumper()),
        );
    }
}