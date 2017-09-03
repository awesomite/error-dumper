<?php

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class UnsafeFunctionsTest extends TestBase
{
    private static $unsafeFunctions
        = array(
            'system',
            'exec',
            'popen',
            'pcntl_exec',
            'eval',
            'create_function',
            'preg_replace', // /e
            'override_function',
            'rename_function',
            'var_dump',
            'print_r',
            'mb_ereg_replace', // /e
            'mb_eregi_replace', // /e
        );

    private static $exclusions
        = array(
            'Handlers/ErrorHandler.php:207' => array('exit'),
            'Editors/Phpstorm.php:33'       => array('preg_replace'),
        );

    /**
     * @dataProvider providerFiles
     */
    public function testPhp(\SplFileInfo $file)
    {
        $filePath = $file->getRealPath();
        foreach (token_get_all(file_get_contents($filePath)) as $tokenArr) {
            if (!is_array($tokenArr)) {
                if ($tokenArr === '`') {
                    $this->fail("Backtick operator is forbidden {$filePath}");
                }
                continue;
            }
            list($token, $source, $line) = $tokenArr;
            $source = strtolower($source);
            $function = $token === T_EXIT
                ? 'exit'
                : $source;
            $explodedPath = explode(DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR, $filePath);
            $lastPart = str_replace('\\', DIRECTORY_SEPARATOR, array_pop($explodedPath));
            $issueKey = $lastPart . ':' . $line;
            if (isset(self::$exclusions[$issueKey]) && in_array($function, self::$exclusions[$issueKey])) {
                continue;
            }

            switch ($token) {
                case T_STRING:
                    if (in_array($source, self::$unsafeFunctions, true)) {
                        $this->fail("Function {$source} in {$filePath}:{$line}");
                    }
                    break;

                case T_EXIT:
                case T_EVAL:
                    $this->fail("Function {$source} in {$filePath}:{$line}");
                    break;
            }
        }
    }

    public function providerFiles()
    {
        $exploded = explode(DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR, __DIR__);
        array_pop($exploded);
        $path = implode('tests', $exploded) . 'src';
        $pattern = '/^.+\.php$/';

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);

        $callback = function (\SplFileInfo $file) use ($pattern) {
            if ($file->isDir()) {
                return false;
            }

            return (bool)preg_match($pattern, $file->getRealPath());
        };

        $result = array();
        $filterIterator = new \CallbackFilterIterator($iterator, $callback);
        foreach ($filterIterator as $file) {
            $result[] = array($file);
        }

        return $result;
    }
}
