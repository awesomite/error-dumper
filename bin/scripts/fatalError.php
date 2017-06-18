<?php

use Awesomite\ErrorDumper\ErrorDumper;

ErrorDumper::createDevHandler()->register();

require_once __DIR__ . DIRECTORY_SEPARATOR . '_fatalError.php.fatal_error';
