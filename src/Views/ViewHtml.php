<?php

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface;
use Awesomite\ErrorDumper\Editors\EditorInterface;

class ViewHtml implements ViewInterface
{
    const TAG_HTML = '<!-- @ErrorDumper -->';
    const TAG_UNDER_TITLE = '<!-- @ErrorDumper under title -->';
    
    private static $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'HTTP/1.1 503 Service Temporarily Unavailable',
        'Status: 503 Service Temporarily Unavailable',
    );

    /**
     * @var EditorInterface|null
     */
    private $editor;

    private $contentUnderTitle;

    private $cacheDirectory;

    private $appendToBody = array();

    /**
     * @param string
     *
     * @see sys_get_temp_dir()
     */
    public function enableCaching($path)
    {
        $this->cacheDirectory = $path;
    }
    
    public function disableCaching()
    {
        $this->cacheDirectory = null;
    }

    public function display(ClonedExceptionInterface $exception)
    {
        // @codeCoverageIgnoreStart
        if (!headers_sent() && php_sapi_name() !== 'cli') {
            foreach (self::$headers as $header) {
                header($header);
            }
        }
        // @codeCoverageIgnoreEnd
        
        $this->createTwig()->display('exception.twig', array(
            'exception' => $exception,
            'tags' => $this->getTags(),
            'resources' => $this->getResources(),
            'editor' => $this->editor,
            'hasEditor' => !is_null($this->editor),
            'contentUnderTitle' => $this->contentUnderTitle,
            'appendToBody' => $this->appendToBody,
        ));
    }

    /**
     * @param string $string
     */
    public function setContentUnderTitle($string)
    {
        $this->contentUnderTitle = $string;
    }

    public function setEditor(EditorInterface $editor)
    {
        $this->editor = $editor;
    }

    /**
     * @param string $string
     */
    public function appendToBody($string)
    {
        $this->appendToBody[] = $string;
    }

    private function createTwig()
    {
        $twigOptions = array();
        if (!is_null($this->cacheDirectory)) {
            $twigOptions['cache'] = $this->cacheDirectory;
        }
        $twig = new \Twig_Environment($this->createTwigLoader(), $twigOptions);
        $twig->addFilter(
            new \Twig_SimpleFilter('strpad', function($input, $padLength, $padString = ' ', $padType = STR_PAD_LEFT) {
                return str_pad($input, $padLength, $padString, $padType);
            })
        );
        $twig->addFunction(new \Twig_SimpleFunction('memoryUsage', function () {
            return number_format(memory_get_peak_usage()/1024/1024, 2) . ' MB';
        }));
        $twig->addFunction(new \Twig_SimpleFunction('exportDeclaredValue', function ($param) {
            return var_export($param, true);
        }));

        return $twig;
    }

    private function createTwigLoader()
    {
        $delimiter = DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        $parts = explode($delimiter, __DIR__);
        array_pop($parts);
        $root = implode($delimiter, $parts);

        return new \Twig_Loader_Filesystem($root . DIRECTORY_SEPARATOR . 'templates');
    }

    private function getTags()
    {
        return array(
            'html' => static::TAG_HTML,
            'underTitle' => static::TAG_UNDER_TITLE,
        );
    }

    private function getResources()
    {
        return array(
            'bootstrapCss' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
            'bootstrapJs' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
            'jqueryJs' => '//code.jquery.com/jquery-2.1.4.min.js',
        );
    }
}