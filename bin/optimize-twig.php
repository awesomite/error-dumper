#!/usr/bin/env php
<?php

use Awesomite\ErrorDumper\Optimizer\Application as OptimizerApplication;

require \implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

OptimizerApplication::run();
