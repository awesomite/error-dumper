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

use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class TemplatesTest extends AbstractTestCase
{
    public function testSingleLine()
    {
        $finder = new Finder();
        $finder
            ->in(\implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'templates_dist')))
            ->name('*.twig');

        $this->assertGreaterThan(0, \count($finder));
        foreach ($finder as $file) {
            $this->assertSame(1, \mb_substr_count($file->getContents(), "\n"), $file->getFilename());
        }
    }
}
