<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Cloners\ClonedException;
use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\TestBase;
use Awesomite\VarDumper\LightVarDumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
class ViewHtmlTest extends TestBase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::warmUp();
    }

    private static function warmUp()
    {
        $view = new ViewHtml();
        $exception = new \Exception();
        ob_start();
        $view->display(new ClonedException($exception, 1));
        ob_end_clean();
    }

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
        $varDumper = new LightVarDumper();
        $varDumper
            ->setMaxChildren(5)
            ->setMaxDepth(3)
            ->setMaxStringLength(50);

        $exception = new ClonedException(new \Exception(), 3);
        $exception->getStackTrace()->setVarDumper($varDumper);

        return array(
            array($exception, null),
            array($exception, new Phpstorm()),
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
        $view->display(new ClonedException(new \Exception(), 1));
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

    /**
     * @dataProvider providerAppendToBody
     *
     * @param $toAppend
     * @param string $expected
     */
    public function testAppendToBody($toAppend, $expected)
    {
        $view = new ViewHtml();
        $view->appendToBody($toAppend);

        ob_start();
        $view->display(new ClonedException(new \Exception(), 1, true));
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertContains($expected, $contents);
    }

    public function providerAppendToBody()
    {
        $rand = mt_rand();
        $scriptTag =<<<SCRIPT
<script type="text/javascript">
    console.log(new Date());
    // rand value {$rand}
</script>
SCRIPT;
        $stringable = new Stringable(function () use ($scriptTag) {
            return $scriptTag;
        });

        $secondScriptTag = <<<SCRIPT
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"></script>
SCRIPT;
        $secondStringable = new Stringable(function () use ($secondScriptTag) {
            return $secondScriptTag;
        });

        return array(
            array($scriptTag, $scriptTag),
            array($stringable, $scriptTag),
            array($secondStringable, $secondScriptTag),
        );
    }
}