<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Serializable;

interface ContextVarInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function dumpAsString();
}
