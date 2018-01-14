<?php

/*
 * This file is part of the awesomite/var-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper;

/**
 * @internal
 */
class TestEnv
{
    public static function isSpeedTest()
    {
        return 'true' === \getenv('MODE_SPEED_TEST');
    }
}
