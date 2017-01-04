<?php

use Awesomite\ErrorDumper\ErrorDumper;

$dumper = new ErrorDumper();
$dumper->createDevHandler()
    ->registerOnError()
    ->registerOnShutdown()
    ->registerOnException();

require_once __DIR__ . DIRECTORY_SEPARATOR . '_fatalError.php';