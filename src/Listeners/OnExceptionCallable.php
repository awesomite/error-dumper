<?php

namespace Awesomite\ErrorDumper\Listeners;

class OnExceptionCallable extends AbstractExceptionEvent implements OnExceptionInterface
{
    public function onException($exception)
    {
        $this->call($exception);
    }
}
