<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Serializable;

use Awesomite\ErrorDumper\AbstractTestCase;

/**
 * @internal
 */
final class ContextVarTest extends AbstractTestCase
{
    /**
     * @dataProvider providerAll
     *
     * @param $name
     * @param $dump
     */
    public function testAll($name, $dump)
    {
        $variable = new ContextVar($name, $dump);

        foreach (array($variable, \unserialize(\serialize($variable))) as $current) {
            /** @var ContextVar $current */
            $this->assertSame($name, $current->getName());
            $this->assertSame($dump, $current->dumpAsString());
        }
    }

    public function providerAll()
    {
        return array(
            array('int', "5\n"),
            array('bool', "false\n"),
        );
    }
}
