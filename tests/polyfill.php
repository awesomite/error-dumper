<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!\function_exists('is_iterable')) {
    /**
     * @internal
     *
     * @param $var
     *
     * @return bool
     *
     * @see https://travis-ci.org/awesomite/error-dumper/jobs/428909243
     */
    function is_iterable($var)
    {
        return \is_array($var) || $var instanceof \Traversable;
    }
}
