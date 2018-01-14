<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Views\ViewInterface;

class OnExceptionDevView implements OnExceptionInterface
{
    private $view;

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function onException($exception)
    {
        $this->view->display(new SerializableException($exception));
    }
}
