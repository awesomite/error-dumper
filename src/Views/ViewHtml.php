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
    const TAG_HTML        = '<!-- @ErrorDumper -->';
    const TAG_UNDER_TITLE = '<!-- @ErrorDumper under title -->';

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

    private $contentUnderTitle;

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
            'contentUnderTitle' => $this->contentUnderTitle,
            'appendToBody'      => $this->appendToBody,
        ));
    }

    /**
     * @param object|string $string
     *
     * @return $this
     */
    public function setContentUnderTitle($string)
    {
        $this->contentUnderTitle = $string;

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
     * @param object|string $string
     *
     * @return $this
     */
    public function appendToBody($string)
    {
        $this->appendToBody[] = $string;

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
                    'link'      => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
                    'integrity' => 'sha384-pdapHxIh7EYuwy6K7iE41uXVxGCXY0sAjBzaElYGJUrzwodck3Lx6IE2lA0rFREo',
                ),
            ),
            'js'  => array(
                array(
                    'link'      => '//code.jquery.com/jquery-2.1.4.min.js',
                    'integrity' => 'sha384-R4/ztc4ZlRqWjqIuvf6RX5yb/v90qNGx6fS48N0tRxiGkqveZETq72KgDVJCp2TC',
                ),
                array(
                    'link'      => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
                    'integrity' => 'sha384-pPttEvTHTuUJ9L2kCoMnNqCRcaMPMVMsWVO+RLaaaYDmfSP5//dP6eKRusbPcqhZ',
                ),
            ),
        );
    }
}
