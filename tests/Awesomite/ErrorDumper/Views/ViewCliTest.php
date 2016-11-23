<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Cloners\ClonedException;
use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 */
class ViewCliTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerDisplay
     *
     * @param ClonedExceptionInterface $clonedException
     */
    public function testDisplay(ClonedExceptionInterface $clonedException)
    {
        $output = new StreamOutput(tmpfile());
        $view = new ViewCli(7, 0, $output);
        $view->display($clonedException);
        $stream = $output->getStream();
        fseek($stream, 0);
        $output = '';
        while (!feof($stream)) {
            $output .= fread($stream, 4096);
        }
        fclose($stream);
        $this->assertRegExp('/' . preg_quote($clonedException->getOriginalClass()) . '/', $output);
    }

    public function providerDisplay()
    {
        return array(
            array(new ClonedException(new \Exception())),
        );
    }
}