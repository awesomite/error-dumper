<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class DevTest extends TestBase
{
    public function testUseTemplatesDist()
    {
        $reflectionClass = new \ReflectionClass('Awesomite\ErrorDumper\Views\ViewHtml');
        $file = \file_get_contents($reflectionClass->getFileName());
        $this->assertContains("'templates_dist'", $file);
        $this->assertContains("'templates'", $file);
    }
}
