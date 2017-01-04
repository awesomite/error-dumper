<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class TestBase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->expectOutputString('');
    }
}