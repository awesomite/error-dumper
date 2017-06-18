<?php

namespace Awesomite\ErrorDumper\Editors;

use Awesomite\ErrorDumper\TestBase;

/**
 * @internal
 */
class PhpstormTest extends TestBase
{
    /**
     * @dataProvider providerGetLinkToFile
     *
     * @param $file
     * @param $line
     */
    public function testGetLinkToFile($file, $line)
    {
        $phpstorm = new Phpstorm();
        $this->assertSame('string', gettype($phpstorm->getLinkToFile($file, $line)));
    }

    public function providerGetLinkToFile()
    {
        return array(
            array(__FILE__, __LINE__),
            array(__FILE__, null),
        );
    }

    /**
     * @dataProvider providerRegisterPathMapping
     *
     * @param string $mapFrom
     * @param string $mapTo
     * @param string $file
     * @param int|null $line
     * @param string $expectedFile
     */
    public function testRegisterPathMapping($mapFrom, $mapTo, $file, $line, $expectedFile)
    {
        $phpstorm = new Phpstorm();
        $phpstorm2 = $phpstorm->registerPathMapping($mapFrom, $mapTo);
        $this->assertSame($phpstorm, $phpstorm2);
        $link = $phpstorm->getLinkToFile($file, $line);
        list(, $stringParams) = explode('?', $link, 2);
        parse_str($stringParams, $data);
        $this->assertEquals($line, isset($data['line']) ? $data['line'] : null);
        $this->assertSame($expectedFile, $data['file']);
    }

    public function providerRegisterPathMapping()
    {
        return array(
            array(__DIR__, '/foo/bar', __FILE__, null, '/foo/bar/PhpstormTest.php'),
            array(__DIR__, '/foo/bar', '/bar/foo/file.php', __LINE__, '/bar/foo/file.php'),
        );
    }
}
