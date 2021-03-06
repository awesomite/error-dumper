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
 *
 * @runTestsInSeparateProcesses
 */
final class SyntaxTest extends AbstractTestCase
{
    public static function requireWholeSrc()
    {
        $path = self::preparePathToDir('src');
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH);
        $counter = 0;
        foreach ($regex as $file) {
            $counter++;
            require_once $file[0];
        }

        return array($path, $counter);
    }

    public function testPhpSyntax()
    {
        list($path, $counter) = static::requireWholeSrc();
        $this->assertInternalType('string', $path);
        $this->assertGreaterThan(0, $counter);
    }

    /**
     * @dataProvider providerTwigSyntax
     *
     * @param string $path
     */
    public function testTwigSyntax($path)
    {
        $this->assertInternalType('string', $path);
        $twig = $this->createTwig($path, array('strpad'), array('memoryUsage', 'exportDeclaredValue'));

        $counter = 0;
        foreach ($this->getRecursiveFileIterator($path, '/^.+\.twig$/') as $file) {
            $fileName = $file[0];

            $counter++;

            try {
                $twig->parse($twig->tokenize($this->getTwigSource($fileName)));
            } catch (\Twig_Error_Syntax $exception) {
                throw new \RuntimeException(
                    $exception->getMessage() . " in file {$fileName}:{$exception->getTemplateLine()}"
                );
            }
        }
        $this->assertGreaterThan(0, $counter);
    }

    public function providerTwigSyntax()
    {
        return array(
            array($this->preparePathToDir('templates')),
            array($this->preparePathToDir('templates_dist')),
        );
    }

    /**
     * @param string $dir
     *
     * @return bool|string
     */
    private static function preparePathToDir($dir)
    {
        $delimiter = \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR;
        $exploded = \explode($delimiter, __FILE__);
        \array_pop($exploded);

        return \realpath(\implode($delimiter, $exploded) . \DIRECTORY_SEPARATOR . $dir);
    }

    /**
     * @param $path
     *
     * @return \Twig_Environment
     */
    private function createTwig($path, array $filters, array $functions)
    {
        $loader = new \Twig_Loader_Filesystem($path);
        $twig = new \Twig_Environment($loader);

        foreach ($filters as $filter) {
            $twig->addFilter(new \Twig_SimpleFilter($filter, function () {
            }));
        }

        foreach ($functions as $function) {
            $twig->addFunction(new \Twig_SimpleFunction($function, function () {
            }));
        }

        return $twig;
    }

    private function getTwigSource($filename)
    {
        $contents = \file_get_contents($filename);

        $envReflection = new \ReflectionClass('Twig_Environment');
        $method = $envReflection->getMethod('tokenize');
        list($firstParameter) = $method->getParameters();
        /** @var \ReflectionParameter $firstParameter */
        if ($firstParameter->getClass()) {
            return new \Twig_Source($contents, $filename);
        }

        return $contents;
    }

    private function getRecursiveFileIterator($path, $pattern)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    }
}
