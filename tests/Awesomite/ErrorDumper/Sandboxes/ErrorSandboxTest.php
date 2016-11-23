<?php

namespace Awesomite\ErrorDumper\Sandboxes;

/**
 * @internal
 */
class ErrorSandboxTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteSafely()
    {
        $sandbox = new ErrorSandbox();
        $sandbox->executeSafely(function () {
            trigger_error('Test');
        });
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testFrameworkError()
    {
        trigger_error('Test');
    }

    /**
     * @expectedException \Awesomite\ErrorDumper\Sandboxes\SandboxException
     */
    public function testExecute()
    {
        $sandbox = new ErrorSandbox();
        $sandbox->execute(function () {});
        $sandbox->execute(function () {
            trigger_error('Test');
        });
    }
}