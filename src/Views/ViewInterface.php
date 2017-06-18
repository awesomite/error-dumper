<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface;

interface ViewInterface
{
    /**
     * @param SerializableExceptionInterface $exception
     * @return mixed
     */
    public function display(SerializableExceptionInterface $exception);
}
