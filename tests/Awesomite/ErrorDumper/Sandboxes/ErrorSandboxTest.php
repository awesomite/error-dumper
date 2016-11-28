<?php

namespace Awesomite\ErrorDumper\Sandboxes;

/**
 * @internal
 */
class ErrorSandboxTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteSafely()
    {
        $executed = false;
        $sandbox = new ErrorSandbox();
        $expectedResult = 123;
        $result = $sandbox->executeSafely(function () use (&$executed, $expectedResult) {
            trigger_error('Test');
            $executed = true;
            return $expectedResult;
        });
        $this->assertTrue($executed);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testFrameworkError()
    {
        trigger_error('Test');
    }

    /**
     * @dataProvider providerExecute
     *
     * @param ErrorSandbox $sandbox
     * @param int $errorType
     * @param bool $expectedExecuted
     * @param bool $expectedThrown
     */
    public function testExecute(ErrorSandbox $sandbox, $errorType, $expectedExecuted, $expectedThrown)
    {
        $executed = false;
        $thrown = false;

        $sandbox->execute(function () {});
        try {
            $expectedResult = 125;
            $result = $sandbox->execute(function () use (&$executed, $errorType, $expectedResult) {
                @trigger_error('Test', $errorType);
                $executed = true;

                return $expectedResult;
            });
            $this->assertSame($expectedResult, $result);
        } catch (SandboxException $exception) {
            $thrown = true;
        }

        $this->assertSame($expectedExecuted, $executed);
        $this->assertSame($expectedThrown, $thrown);
    }

    public function providerExecute()
    {
        return array(
            array(new ErrorSandbox(), E_USER_DEPRECATED, false, true),
            array(new ErrorSandbox(E_ALL ^ E_USER_WARNING), E_USER_WARNING, true, false),
        );
    }
}