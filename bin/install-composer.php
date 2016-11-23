#!/usr/bin/env php
<?php

$destinationFile = 'composer-setup.php';
copy('https://getcomposer.org/installer', 'composer-setup.php');
$checkSum = 'aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d';
if (hash_file('SHA384', 'composer-setup.php') === $checkSum) {
    echo 'Installer verified' . PHP_EOL;
    require_once $destinationFile;
    return;
}

throw new \RuntimeException('Installer corrupt');