#!/usr/bin/env php
<?php

global $argv;

$handle = function ($scriptName, $key, $value) {
    $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'composer.json';
    $json = json_decode(file_get_contents($jsonPath), true);
    if (!isset($json['config'])) {
        $json['config'] = array();
    }
    $json['config'][$key] = $value;

    $options = JSON_UNESCAPED_UNICODE;
    if (defined('JSON_PRETTY_PRINT')) {
        $options |= constant('JSON_PRETTY_PRINT');
    }
    file_put_contents($jsonPath, json_encode($json, $options));
};

call_user_func_array($handle, $argv);
