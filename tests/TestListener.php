<?php

namespace Awesomite\ErrorDumper;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 */
class TestListener implements \PHPUnit_Framework_TestListener
{
    private $offset = .1;

    private static $messages = array();

    public static function addMessage($message)
    {
        self::$messages[] = $message;
    }

    public function __destruct()
    {
        $output = new ConsoleOutput();
        foreach (self::$messages as $message) {
            $output->writeln($message);
        }
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if ($time < $this->offset) {
            return;
        }

        $name = $test instanceof \PHPUnit_Framework_TestCase
            ? get_class($test) . '::' . $test->getName()
            : get_class($test);

        $message = sprintf("\n<error>Test '%s' ended and took %0.2f seconds.</error>",
            $name,
            $time
        );

        static::addMessage($message);
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }
}