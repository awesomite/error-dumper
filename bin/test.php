#!/usr/bin/env php
<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'vendor', 'autoload.php'));

require implode(DIRECTORY_SEPARATOR, array(__DIR__, 'scripts', 'testStackTrace.php'));
