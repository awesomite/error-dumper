#!/usr/bin/env php
<?php

$destinationFile = 'composer-setup.php';
copy('https://getcomposer.org/installer', 'composer-setup.php');
$checkSum = '55d6ead61b29c7bdee5cccfb50076874187bd9f21f65d8991d46ec5cc90518f447387fb9f76ebae1fbbacf329e583e30';
if (hash_file('SHA384', 'composer-setup.php') === $checkSum) {
    echo 'Installer verified' . PHP_EOL;
    require_once $destinationFile;
    return;
}

throw new \RuntimeException('Installer corrupt');