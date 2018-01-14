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

interface EditorInterface
{
    /**
     * @param string   $filename
     * @param int|null $line
     *
     * @return string
     */
    public function getLinkToFile($filename, $line = null);

    /**
     * @param string $serverPath
     * @param string $projectPath
     *
     * @return EditorInterface
     */
    public function registerPathMapping($serverPath, $projectPath);
}
