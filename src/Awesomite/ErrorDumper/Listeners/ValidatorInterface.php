<?php

namespace Awesomite\ErrorDumper\Listeners;

interface ValidatorInterface
{
    /**
     * @param \Exception|\Throwable $exception
     * @return void
     */
    public function onBeforeException($exception);

    /**
     * @throws StopPropagationException
     */
    public function stopPropagation();
}