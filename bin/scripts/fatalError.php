<?php

use Awesomite\ErrorDumper\ErrorDumper;

$dumper = new ErrorDumper();
$dumper->createDevHandler()->register();

require_once __DIR__ . DIRECTORY_SEPARATOR . '_fatalError.php.fatal_error';