<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class SyntaxTest extends TestBase
{
    public function testSyntax()
    {
        $path = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', '..', 'src')));
        $this->assertInternalType('string', $path);

        $counter = 0;
        foreach ($this->getRecursiveFileIterator($path, '/^.+\.php$/') as $file) {
            $counter++;
            require_once $file[0];
        }
        $this->assertGreaterThan(0, $counter);
    }

    public function testTwigSyntax()
    {
        $path = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', '..', 'templates')));
        $this->assertInternalType('string', $path);

        $loader = new \Twig_Loader_Filesystem($path);
        $twig = new \Twig_Environment($loader);
        $twig->addFilter(new \Twig_SimpleFilter('strpad', function () {}));
        $twig->addFunction(new \Twig_SimpleFunction('memoryUsage', function () {}));
        $twig->addFunction(new \Twig_SimpleFunction('exportDeclaredValue', function () {}));

        $counter = 0;
        foreach ($this->getRecursiveFileIterator($path, '/^.+\.twig$/') as $file) {
            $fileName = $file[0];

            $counter++;
            try {
                $twig->parse($twig->tokenize(file_get_contents($fileName)));
            } catch (\Twig_Error_Syntax $exception) {
                throw new \RuntimeException(
                    $exception->getMessage() . " in file {$fileName}:{$exception->getTemplateLine()}"
                );
            }
        }
        $this->assertGreaterThan(0, $counter);
    }

    private function getRecursiveFileIterator($path, $pattern)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    }
}