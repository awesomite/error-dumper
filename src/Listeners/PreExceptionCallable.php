<?php

namespace Awesomite\ErrorDumper\Listeners;

class PreExceptionCallable extends AbstractExceptionEvent implements PreExceptionInterface
{
    public static function stopPropagation()
    {
        throw new StopPropagationException();
    }

    public function preException($exception)
    {
        $this->call($exception);
    }
}
