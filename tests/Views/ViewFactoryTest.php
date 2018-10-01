<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\AbstractTestCase;

/**
 * @internal
 */
final class ViewFactoryTest extends AbstractTestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('Awesomite\ErrorDumper\Views\ViewInterface', ViewFactory::create());
    }
}
