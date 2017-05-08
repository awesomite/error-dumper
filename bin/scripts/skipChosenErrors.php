<?php

use Awesomite\ErrorDumper\ErrorDumper;

ErrorDumper::createDevHandler(E_ALL ^ E_USER_DEPRECATED)->register();

trigger_error('Test error', E_USER_DEPRECATED);
return 'OK';
