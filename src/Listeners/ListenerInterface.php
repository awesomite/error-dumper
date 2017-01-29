<?php

namespace Awesomite\ErrorDumper\Listeners;

interface ListenerInterface
{
    /**
     * @param \Throwable|\Exception $exception
     * @return void
     */
    public function onException($exception);
}