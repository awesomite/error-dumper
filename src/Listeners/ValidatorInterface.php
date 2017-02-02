<?php

namespace Awesomite\ErrorDumper\Listeners;

interface ValidatorInterface
{
    /**
     * @param \Exception|\Throwable $exception
     * @return void
     * @throws StopPropagationException
     */
    public function onBeforeException($exception);
}