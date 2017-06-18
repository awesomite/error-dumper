<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class SyntaxTest extends TestBase
{
    public function testPhpSyntax()
    {
        $path = $this->preparePathToDir('src');
        $this->assertInternalType('string', $path);

        $counter = 0;
        foreach ($this->getRecursiveFileIterator($path, '/^.+\.php$/') as $file) {
            $counter++;
            require_once $file[0];
        }
        $this->assertGreaterThan(0, $counter);
    }

    /**
     * @runInSeparateProcess
     */
    public function testTwigSyntax()
    {
        $path = $this->preparePathToDir('templates');
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

    /**
     * @param $path
     * @return \Twig_Environment
     */
    private function createTwig($path, array $filters, array $functions)
    {
        $loader = new \Twig_Loader_Filesystem($path);
        $twig = new \Twig_Environment($loader);

        foreach ($filters as $filter) {
            $twig->addFilter(new \Twig_SimpleFilter($filter, function () {}));
        }

        foreach ($functions as $function) {
            $twig->addFunction(new \Twig_SimpleFunction($function, function () {}));
        }

        return $twig;
    }

    private function getTwigSource($filename)
    {
        $contents = file_get_contents($filename);

        $envReflection = new \ReflectionClass('Twig_Environment');
        $method = $envReflection->getMethod('tokenize');
        list($firstParameter) = $method->getParameters();
        /** @var \ReflectionParameter $firstParameter */

        if ($firstParameter->getClass()) {
            return new \Twig_Source($contents, $filename);
        }

        return $contents;
    }

    /**
     * @param string $dir
     * @return string|bool
     */
    private function preparePathToDir($dir)
    {
        $delimiter = DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;
        $exploded = explode($delimiter, __FILE__);
        array_pop($exploded);

        return realpath(implode($delimiter, $exploded) . DIRECTORY_SEPARATOR . $dir);
    }

    private function getRecursiveFileIterator($path, $pattern)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    }
}
