#!/usr/bin/env php
<?php

use Awesomite\ErrorDumper\Optimizer\Application as OptimizerApp;
use Symfony\Component\Finder\Finder;

require \implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

$checkSumReader = function ($templatesDir) {
    $command = \sprintf(
        'find %s -type f -exec md5sum {} \; | sort -k 2 | md5sum',
        $templatesDir
    );
    
    echo $command, "\n";
    $result = \shell_exec($command);
    echo $result, "\n";
    
    $result = \trim($result);
    $result = \trim($result, '-');
    $result = \trim($result);
    
    return $result;
};

$getTemplatesDir = function () {
    return \implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'templates', 'optimized'));
};

$rmDirContents = function ($dir) {
    $finder = new Finder();
    $finder
        ->in($dir)
        ->name('*.twig')
    ;
    foreach ($finder as $file) {
        echo $file->getRealPath(), "\n";
        \unlink($file->getRealPath());
    }
};

$templatesDir = $getTemplatesDir();

$checkSumA = $checkSumReader($templatesDir);
$rmDirContents($templatesDir);
OptimizerApp::run();
$checkSumB = $checkSumReader($templatesDir);

if ($checkSumA !== $checkSumB) {
    echo "Execute `bin/optimize-twig.php`, then commit generated files\n";
    exit(1);
} else {
    echo "Success\n";
}
