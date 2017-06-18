<?php

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
