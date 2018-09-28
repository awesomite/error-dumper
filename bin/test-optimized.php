#!/usr/bin/env php
<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Optimizer\Application as OptimizerApp;
use Symfony\Component\Finder\Finder;

require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

$createFinder = function ($dir) {
    $finder = new Finder();
    $finder
        ->in($dir)
        ->name('*.twig')
    ;

    return $finder;
};

$checkSumReader = function ($templatesDir) use ($createFinder) {
    /** @var Finder $finder */
    $finder = $createFinder($templatesDir);
    $md5s = array();
    foreach ($finder as $file) {
        $md5s[] = \md5($file->getContents());
    }

    return \md5(\implode('-', $md5s));
};

$getTemplatesDir = function () {
    return \implode(\DIRECTORY_SEPARATOR, array(__DIR__, '..', 'templates_dist'));
};

$rmDirContents = function ($dir) use ($createFinder) {
    /** @var Finder $finder */
    $finder = $createFinder($dir);
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
