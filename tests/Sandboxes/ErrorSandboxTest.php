<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Sandboxes;

use Awesomite\ErrorDumper\AbstractTestCase;

/**
 * @internal
 */
final class ErrorSandboxTest extends AbstractTestCase
{
    /**
     * @dataProvider providerExecuteSafely
     *
     * @param $expectedResult
     */
    public function testExecuteSafely($expectedResult)
    {
        $executed = false;
        $sandbox = new ErrorSandbox();
        $result = $sandbox->executeSafely(function () use (&$executed, $expectedResult) {
            \trigger_error('Test');
            $executed = true;

            return $expectedResult;
        });
        $this->assertTrue($executed);
        $this->assertSame($expectedResult, $result);
    }

    public function providerExecuteSafely()
    {
        return array(
            array(123),
            array(false),
            array(\M_PI),
            array(null),
            array(new \stdClass()),
            array($this),
        );
    }

    public function testThrowable()
    {
        /**
         * Cannot be executed on HHVM
         * https://travis-ci.org/awesomite/error-dumper/jobs/434719255
         */
        if (\defined('HHVM_VERSION') && \version_compare(\PHP_VERSION, '7.0') < 0) {
            $this->markTestSkipped('Must be executed on php >= 7.0');
        }
        $sandbox = new ErrorSandbox();
        $caught = false;

        try {
            $sandbox->execute(function () {
                eval('<?php class AwesomeClass {');
            });
        } catch (\ParseError $exception) {
            $caught = true;
        }
        $this->assertTrue($caught);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testFrameworkError()
    {
        \trigger_error('Test');
    }

    /**
     * @dataProvider providerExecute
     *
     * @param ErrorSandbox $sandbox
     * @param int          $errorType
     * @param bool         $expectedExecuted
     * @param bool         $expectedThrown
     */
    public function testExecute(ErrorSandbox $sandbox, $errorType, $expectedExecuted, $expectedThrown)
    {
        $executed = false;
        $thrown = false;

        $sandbox->execute(function () {
        });

        try {
            $expectedResult = 125;
            $result = $sandbox->execute(function () use (&$executed, $errorType, $expectedResult) {
                @\trigger_error('Test', $errorType);
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
            array(new ErrorSandbox(), \E_USER_DEPRECATED, false, true),
            array(new ErrorSandbox(\E_ALL ^ \E_USER_WARNING), \E_USER_WARNING, true, false),
        );
    }
}
