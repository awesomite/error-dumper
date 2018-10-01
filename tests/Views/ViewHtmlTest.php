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
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface;
use Awesomite\VarDumper\LightVarDumper;

/**
 * @internal
 */
final class ViewHtmlTest extends AbstractTestCase
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
        \ob_start();
        $view->display(new SerializableException($exception, 1));
        \ob_end_clean();
    }

    public function testDistTemplates()
    {
        $exception = new \RuntimeException('My test exception', 5);
        $serializable = new SerializableException($exception, 0, false, true, false);

        $compressedHtml = $this->printToVar(function () use ($serializable) {
            $view = new ViewHtml();
            $view->display($serializable);
        });

        $compressedHtml2 = $this->printToVar(function () use ($serializable) {
            $view = new ViewHtml();
            $view->useDistTemplates();
            $view->display($serializable);
        });

        $html = $this->printToVar(function () use ($serializable) {
            $view = new ViewHtml();
            $view->useSrcTemplates();
            $view->display($serializable);
        });

        $this->assertSame(\strlen($compressedHtml), \strlen($compressedHtml2));
        $this->assertTrue(\mb_strlen($html) > \mb_strlen($compressedHtml));
    }

    /**
     * @dataProvider providerDisplay
     *
     * @param SerializableExceptionInterface $clonedException
     * @param null|EditorInterface           $editor
     */
    public function testDisplay(SerializableExceptionInterface $clonedException, EditorInterface $editor = null)
    {
        $view = new ViewHtml();
        $this->assertSame($view, $view->setEditor($editor));
        \ob_start();
        $view->display($clonedException);
        $contents = \ob_get_contents();
        \ob_get_clean();
        $this->assertContains(ViewHtml::TAG_HTML, $contents);
    }

    public function providerDisplay()
    {
        $varDumper = new LightVarDumper();
        $varDumper
            ->setMaxChildren(5)
            ->setMaxDepth(3)
            ->setMaxStringLength(50);

        $exception = new SerializableException(new \Exception(), 3);
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
        $this->assertSame($view, $view->setContentUnderTitle($content));
        \ob_start();
        $view->display(new SerializableException(new \Exception(), 1));
        $output = \ob_get_contents();
        \ob_end_clean();
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
     * @dataProvider providerAppendToBody
     *
     * @param        $toAppend
     * @param string $expected
     */
    public function testAppendToBody($toAppend, $expected)
    {
        $view = new ViewHtml();
        $this->assertSame($view, $view->appendToBody($toAppend));

        \ob_start();
        $view->display(new SerializableException(new \Exception(), 1, true));
        $contents = \ob_get_contents();
        \ob_end_clean();
        $this->assertContains($expected, $contents);
    }

    public function providerAppendToBody()
    {
        $rand = \mt_rand();
        $scriptTag
            = <<<SCRIPT
<script type="text/javascript">
    console.log(new Date());
    // rand value {$rand}
</script>
SCRIPT;
        $stringable = new Stringable(function () use ($scriptTag) {
            return $scriptTag;
        });

        $secondScriptTag
            = <<<SCRIPT
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

    public function testEnableDisableHeaders()
    {
        $view = new ViewHtml();
        $this->assertSame($view, $view->disableHeaders());
        $this->assertSame($view, $view->enableHeaders());
    }

    private function printToVar($callable)
    {
        \ob_start();
        $callable();
        $result = \ob_get_contents();
        \ob_end_clean();

        return $result;
    }
}
