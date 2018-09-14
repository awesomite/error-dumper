<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Views;

/**
 * @internal
 */
class TwigLoader extends \Twig_Loader_Filesystem
{
    public function getSource($name)
    {
        return \trim(parent::getSource($name));
    }
}
