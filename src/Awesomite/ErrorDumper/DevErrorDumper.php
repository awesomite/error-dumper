<?php

namespace Awesomite\ErrorDumper;

use Awesomite\ErrorDumper\Cloners\ClonedException;
use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;

class DevErrorDumper extends AbstractErrorDumper
{
    /**
     * @codeCoverageIgnore
     *
     * DevErrorDumper constructor.
     * @param EditorInterface $editor
     * @param int $mode Default E_ALL | E_STRICT
     */
    public function __construct($mode = null, EditorInterface $editor = null)
    {
        $self = $this;
        $this->errorHandler = new ErrorHandler(function ($exception) use ($self, $editor) {
            /** @var \Throwable|\Exception $exception */;
            $clone = new ClonedException($exception, 0, false, false);
            switch (php_sapi_name()) {
                case 'cli':
                    $self->displayCli($clone);
                    break;
                default:
                    if (!headers_sent()) {
                        header('HTTP/1.1 503 Service Temporarily Unavailable');
                        header('Status: 503 Service Temporarily Unavailable');
                    }
                    if (is_null($editor)) {
                        $editor = new Phpstorm();
                    }
                    $self->displayHtml($clone, $editor);
                    break;
            }
            exit(1);
        }, $mode);
    }
}