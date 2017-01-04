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
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH);
        $counter = 0;
        foreach ($regex as $file) {
            $counter++;
            require_once $file[0];
        }
        $this->assertGreaterThan(0, $counter);
    }
}