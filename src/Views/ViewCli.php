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

use Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface;
use Awesomite\StackTrace\Steps\StepInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ViewCli implements ViewInterface
{
    private $stepLimit;

    private $lineLimit;

    private $output;

    /**
     * @param int                  $lineLimit
     * @param int                  $stepLimit
     * @param null|OutputInterface $output
     */
    public function __construct($lineLimit = 7, $stepLimit = 0, OutputInterface $output = null)
    {
        $this->lineLimit = (int)$lineLimit;
        $this->stepLimit = (int)$stepLimit;
        if ($output) {
            $this->setFormaters($output);
            $this->output = $output;
        }
    }

    public function display(SerializableExceptionInterface $exception)
    {
        $output = $this->getOutput();
        $formatter = new OutputFormatter();

        $header = "  {$exception->getOriginalClass()} (code {$exception->getCode()})";
        if ('' !== $msg = $exception->getMessage()) {
            $header .= ' ' . $msg;
        }
        $header .= '  ';
        $emptyLine = \str_pad('', \mb_strlen($header), ' ');
        $output->writeln("<header>{$emptyLine}</header>");
        $output->writeln("<header>{$formatter->escape($header)}</header>");
        $output->writeln("<header>{$emptyLine}</header>");
        $output->writeln('');

        $output->writeln((string)$exception->getStackTrace());

        $stepNo = 0;
        foreach ($exception->getStackTrace() as $step) {
            $output->writeln('');
            $this->renderStep($step, $output, $formatter);
            $stepNo++;
            if ($stepNo === $this->stepLimit) {
                break;
            }
        }
    }

    private function renderStep(
        StepInterface $step,
        OutputInterface $output,
        OutputFormatter $formatter
    ) {
        if ($step->hasCalledFunction()) {
            $output->writeln("<hcode>{$step->getCalledFunction()->getName()}()</hcode>");
        }
        if ($step->hasPlaceInCode()) {
            $placeInCode = $step->getPlaceInCode();

            $fileName = $placeInCode->getFileName();
            $exploded = \explode(\DIRECTORY_SEPARATOR, $fileName);
            $last3 = \array_slice($exploded, -3);
            $shortFileName = \implode(\DIRECTORY_SEPARATOR, $last3);
            if ($fileName !== $shortFileName) {
                $shortFileName = '(...)' . \DIRECTORY_SEPARATOR . $shortFileName;
            }

            $output->writeln("<hcode>{$shortFileName}:{$placeInCode->getLineNumber()}</hcode>");
            $lines = $placeInCode->getAdjacentCode($this->lineLimit);
            $lastLineIndex = $lines->getLastLineIndex();
            foreach ($lines as $line) {
                $lineNumber = \str_pad($line->getLineNumber(), \mb_strlen($lastLineIndex), ' ');
                $escaped = $formatter->escape((string)$line);
                $message = "#{$lineNumber}     {$escaped}";
                $tag = $line->getLineNumber() === $placeInCode->getLineNumber() ? 'ecode' : 'code';
                $output->writeln("<{$tag}>{$message}</{$tag}>");
            }
        }
    }

    private function getOutput()
    {
        // @codeCoverageIgnoreStart
        if (!$this->output) {
            $this->output = $this->createOutput();
        }
        // @codeCoverageIgnoreEnd

        return $this->output;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return ConsoleOutput
     */
    private function createOutput()
    {
        $output = new ConsoleOutput();
        $this->setFormaters($output);

        return $output;
    }

    private function setFormaters(OutputInterface $output)
    {
        $styles = array(
            'header' => new OutputFormatterStyle('white', 'red', array('bold')),
            'hcode'  => new OutputFormatterStyle('green', 'black'),
            'code'   => new OutputFormatterStyle(),
            'ecode'  => new OutputFormatterStyle('white', 'red'),
        );
        $formater = $output->getFormatter();
        foreach ($styles as $name => $style) {
            if (!$formater->hasStyle($name)) {
                $formater->setStyle($name, $style);
            }
        }
    }
}
