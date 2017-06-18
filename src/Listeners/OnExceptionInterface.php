<?php

namespace Awesomite\ErrorDumper\Listeners;

interface OnExceptionInterface
{
    /**
     * @param \Throwable|\Exception $exception
     * @return void
     */
    public function onException($exception);
}
