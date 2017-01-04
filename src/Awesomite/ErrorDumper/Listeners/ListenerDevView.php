<?php

namespace Awesomite\ErrorDumper\Listeners;

use Awesomite\ErrorDumper\Cloners\ClonedException;
use Awesomite\ErrorDumper\Views\ViewInterface;

class ListenerDevView implements ListenerInterface
{
    private $view;

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function onException($exception)
    {
        $this->view->display(new ClonedException($exception));
    }
}