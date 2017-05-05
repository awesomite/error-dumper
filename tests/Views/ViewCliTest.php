<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface;
use Awesomite\ErrorDumper\TestBase;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 */
class ViewCliTest extends TestBase
{
    /**
     * @dataProvider providerDisplay
     *
     * @param SerializableExceptionInterface $clonedException
     * @param int $stepLimit
     */
    public function testDisplay(SerializableExceptionInterface $clonedException, $stepLimit)
    {
        $output = new StreamOutput(tmpfile());
        $view = new ViewCli(7, $stepLimit, $output);
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
            array(new SerializableException(new \LogicException()), 1),
            array(new SerializableException(new \InvalidArgumentException()), 0),
        );
    }
}