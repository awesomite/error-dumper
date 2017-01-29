<?php

namespace Awesomite\ErrorDumper\Editors;

interface EditorInterface
{
    /**
     * @param string $filename
     * @param int|null $line
     * @return string
     */
    public function getLinkToFile($filename, $line = null);

    /**
     * @param string $serverPath
     * @param string $projectPath
     * @return EditorInterface
     */
    public function registerPathMapping($serverPath, $projectPath);
}