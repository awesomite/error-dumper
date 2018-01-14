<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Editors;

class Phpstorm implements EditorInterface
{
    private $mapping = array();

    public function getLinkToFile($filename, $line = null)
    {
        $params = array(
            'file' => $this->convertPath($filename),
        );
        if (!\is_null($line)) {
            $params['line'] = $line;
        }

        return $this->getProtocol() . '://open?' . \http_build_query($params);
    }

    public function registerPathMapping($serverPath, $projectPath)
    {
        $this->mapping[$serverPath] = $projectPath;

        return $this;
    }

    private function convertPath($path)
    {
        $result = $path;
        foreach ($this->mapping as $from => $to) {
            $pattern = '#^' . \preg_quote($from, '#') . '#';
            $result = \preg_replace($pattern, $to, $result);
        }

        return $result;
    }

    private function getProtocol()
    {
        return 'phpstorm';
    }
}
