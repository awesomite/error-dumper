<?php

use Awesomite\ErrorDumper\ErrorDumper;

ErrorDumper::createDevHandler()->register();

trigger_error('Test error', E_USER_WARNING);
return 'OK';
