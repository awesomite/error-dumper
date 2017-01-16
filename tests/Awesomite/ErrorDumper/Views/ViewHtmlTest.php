<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Cloners\ClonedException;
use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\TestBase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
class ViewHtmlTest extends TestBase
{
    /**
     * @dataProvider providerDisplay
     *
     * @param ClonedExceptionInterface $clonedException
     * @param EditorInterface|null $editor
     */
    public function testDisplay(ClonedExceptionInterface $clonedException, EditorInterface $editor = null)
    {
        $view = new ViewHtml();
        if ($editor) {
            $view->setEditor($editor);
        }
        ob_start();
        $view->display($clonedException);
        $contents = ob_get_contents();
        ob_get_clean();
        $this->assertContains(ViewHtml::TAG_HTML, $contents);
    }

    public function providerDisplay()
    {
        return array(
            array(new ClonedException(new \Exception()), null),
            array(new ClonedException(new \Exception()), new Phpstorm()),
        );
    }

    /**
     * @dataProvider providerContentUnderTitle
     *
     * @param string $content
     */
    public function testContentUnderTitle($content)
    {
        $view = new ViewHtml();
        $view->setContentUnderTitle($content);
        ob_start();
        $view->display(new ClonedException(new \Exception()));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($content, $output);
    }

    public function providerContentUnderTitle()
    {
        return array(
            array('<a href="http://localhost:8001">Test</a>'),
            array('Test'),
        );
    }

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
        $this->assertSame(0, count($finder));

        $view = new ViewHtml();
        $view->enableCaching($cachePath);
        ob_start();
        $view->display(new ClonedException(new \Exception()));
        ob_end_clean();
        $this->assertGreaterThan(0, count($finder));
        $filesystem->remove($finder);

        $view->disableCaching();
        ob_start();
        $view->display(new ClonedException(new \Exception()));
        ob_end_clean();
        $this->assertSame(0, count($finder));
    }

    private function getCachePath()
    {
        $exploded = explode(DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR, __DIR__);
        array_pop($exploded);
        $exploded = array_merge($exploded, array('tests', 'cache'));

        return realpath(implode(DIRECTORY_SEPARATOR, $exploded));
    }
}