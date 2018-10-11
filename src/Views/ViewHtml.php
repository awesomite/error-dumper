<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Awesomite\ErrorDumper\Views;

use Awesomite\ErrorDumper\Editors\EditorInterface;
use Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface;

class ViewHtml implements ViewInterface
{
    const TAG_HTML       = '<!-- @ErrorDumper -->';
    const TAG_UNDER_MENU = '<!-- @ErrorDumper under menu -->';

    private static $headers
        = array(
            'Content-Type: text/html; charset=UTF-8',
            'HTTP/1.1 503 Service Temporarily Unavailable',
            'Status: 503 Service Temporarily Unavailable',
        );

    /**
     * @var null|EditorInterface
     */
    private $editor;

    private $contentUnderMenu;

    private $cacheDirectory;

    private $appendToBody = array();

    private $headersEnabled = true;

    private $useDist = true;

    /**
     * @param string
     *
     * @return $this
     *
     * @see sys_get_temp_dir()
     */
    public function enableCaching($path)
    {
        $this->cacheDirectory = $path;

        return $this;
    }

    public function disableCaching()
    {
        $this->cacheDirectory = null;

        return $this;
    }

    public function display(SerializableExceptionInterface $exception)
    {
        // @codeCoverageIgnoreStart
        if ($this->headersEnabled && !\headers_sent() && 'cli' !== \php_sapi_name()) {
            foreach (self::$headers as $header) {
                \header($header);
            }
        }
        // @codeCoverageIgnoreEnd

        $this->createTwig()->display('exception.twig', array(
            'exception'         => $exception,
            'resources'         => $this->getResources(),
            'editor'            => $this->editor,
            'hasEditor'         => !\is_null($this->editor),
            'contentUnderMenu'  => $this->contentUnderMenu,
            'appendToBody'      => $this->appendToBody,
        ));
    }

    /**
     * @param object|string $stringable
     *
     * @return $this
     */
    public function setContentUnderMenu($stringable)
    {
        $this->contentUnderMenu = $stringable;

        return $this;
    }

    /**
     * Argument $editor is optional, because there is not other option to make argument nullable in php < 7.1
     *
     * @param null|EditorInterface $editor
     *
     * @return $this
     */
    public function setEditor(EditorInterface $editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @param object|string $stringable
     *
     * @return $this
     */
    public function appendToBody($stringable)
    {
        $this->appendToBody[] = $stringable;

        return $this;
    }

    /**
     * Do not send error headers during view is rendered.
     * By default ViewHtml sends headers.
     *
     * @return $this
     */
    public function disableHeaders()
    {
        $this->headersEnabled = false;

        return $this;
    }

    /**
     * @see ViewHtml::disableHeaders()
     *
     * @return $this
     */
    public function enableHeaders()
    {
        $this->headersEnabled = true;

        return $this;
    }

    public function useDistTemplates()
    {
        $this->useDist = true;

        return $this;
    }

    public function useSrcTemplates()
    {
        $this->useDist = false;

        return $this;
    }

    private function createTwig()
    {
        $twigOptions = array();
        if (!\is_null($this->cacheDirectory)) {
            $twigOptions['cache'] = $this->cacheDirectory;
        }
        $twig = new \Twig_Environment($this->createTwigLoader(), $twigOptions);
        $twig->addFilter(
            new \Twig_SimpleFilter('strpad', function ($input, $padLength, $padString = ' ', $padType = \STR_PAD_LEFT) {
                return \str_pad($input, $padLength, $padString, $padType);
            })
        );
        $twig->addFunction(new \Twig_SimpleFunction('memoryUsage', function () {
            return \number_format(\memory_get_peak_usage() / 1024 / 1024, 2) . ' MB';
        }));
        $twig->addFunction(new \Twig_SimpleFunction('exportDeclaredValue', function ($param) {
            return \var_export($param, true);
        }));

        return $twig;
    }

    private function createTwigLoader()
    {
        $delimiter = \DIRECTORY_SEPARATOR . 'src' . \DIRECTORY_SEPARATOR;
        $parts = \explode($delimiter, __DIR__);
        \array_pop($parts);
        $root = \implode($delimiter, $parts);

        $path = \implode(\DIRECTORY_SEPARATOR, array($root, $this->useDist ? 'templates_dist' : 'templates'));

        return new \Twig_Loader_Filesystem($path);
    }

    private function getResources()
    {
        return array(
            'css' => array(
                array(
                    'link'      => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
                    'integrity' => 'sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm',
                ),
            ),
            'js'  => array(
                array(
                    'link'      => 'https://code.jquery.com/jquery-3.2.1.slim.min.js',
                    'integrity' => 'sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN',
                ),
                array(
                    'link'      => 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
                    'integrity' => 'sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q',
                ),
                array(
                    'link'      => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
                    'integrity' => 'sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl',
                ),
            ),
        );
    }
}
