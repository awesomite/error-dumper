#!/usr/bin/env php
<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Client as Guzzle;

require \implode(\DIRECTORY_SEPARATOR, array(__DIR__, 'vendor', 'autoload.php'));

$guzzle = new Guzzle();
$response = $guzzle->get(
    'http://localhost:8001/exceptionChain',
    array(
        'http_errors' => false,
        'headers' => array(
            'Host' => 'example.awesomite.local',
        ),
    )
);

$html = \str_replace(
    \realpath(__DIR__ . \DIRECTORY_SEPARATOR . '..'),
    '(...)',
    (string)$response->getBody()
);

\file_put_contents('example.html', $html);
