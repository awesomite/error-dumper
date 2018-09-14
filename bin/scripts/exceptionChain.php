<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\ErrorDumper\Serializable\SerializableException;

/**
 * @internal
 */
class CalculatorException extends Exception
{
}

/**
 * @internal
 */
class Calculator
{
    public function divide($a, $b)
    {
        if (0 === $b) {
            throw new CalculatorException('Division by zero');
        }

        return $a/$b;
    }
}

class Executor
{
    public function execute($callable, $arguments)
    {
        try {
            \call_user_func_array($callable, $arguments);
        } catch (\Exception $exception) {
            throw new \RuntimeException(\sprintf('Uncaught exception %s', \get_class($exception)), 0, $exception);
        }
    }
}

$executor = new Executor();

try {
    $executor->execute(array(new Calculator(), 'divide'), array(5, 0));
} catch (\Exception $exception) {
    $view = new ViewHtml();
    $view->display(new SerializableException($exception));
    exit;
}
