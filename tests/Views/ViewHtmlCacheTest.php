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
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 * @runTestsInSeparateProcesses
 */
final class ViewHtmlCacheTest extends AbstractTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testCache()
    {
        $cachePath = $this->getCachePath();
        $this->assertInternalType('string', $cachePath);

        $finder = new Finder();
        $filesystem = new Filesystem();
        $finder
            ->ignoreDotFiles(true)
            ->in($cachePath);
        $filesystem->remove($finder);
        $this->assertSame(0, \count($finder));

        $view = new ViewHtml();
        $this->assertSame($view, $view->enableCaching($cachePath));
        \ob_start();
        $view->display(new SerializableException(new \Exception()));
        \ob_end_clean();
        $this->assertGreaterThan(0, \count($finder));
        $filesystem->remove($finder);

        $this->assertSame($view, $view->disableCaching());
        \ob_start();
        $view->display(new SerializableException(new \Exception()));
        \ob_end_clean();
        $this->assertSame(0, \count($finder));
    }

    private function getCachePath()
    {
        $exploded = \explode(DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR, __DIR__);
        \array_pop($exploded);
        $exploded = \array_merge($exploded, array('tests', 'cache'));

        return \realpath(\implode(DIRECTORY_SEPARATOR, $exploded));
    }
}
