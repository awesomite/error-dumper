<?php

namespace Awesomite\ErrorDumper\Listeners;

interface PreExceptionInterface
{
    /**
     * @param \Exception|\Throwable $exception
     * @return void
     * @throws StopPropagationException
     */
    public function preException($exception);
}
