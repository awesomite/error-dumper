#!/usr/bin/env php
<?php

require \implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

$reflection = new ReflectionClass('PHPUnit_Util_Getopt');
$file = $reflection->getFileName();
$contents = \file_get_contents($file);

$contents = \str_replace('= each(', '= awesomite_each(', $contents);
\file_put_contents($file, $contents);
