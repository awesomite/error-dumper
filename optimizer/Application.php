<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Optimizer;

/**
 * @internal
 */
final class Application
{
    /**
     * @param null|resource $output
     */
    public static function run($output = null)
    {
        if (null === $output) {
            $output = \fopen('php://output', 'w');
        }

        $optimizer = new OptimizerCommand();
        $optimizer->run($output);

        \fwrite($output, "Done\n");
    }
}
