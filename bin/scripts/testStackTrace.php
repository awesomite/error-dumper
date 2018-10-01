<?php

/*
 * This file is part of the awesomite/error-dumper package.
 *
 * (c) Bartłomiej Krukowski <bartlomiej@krukowski.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Awesomite\ErrorDumper\Editors\Phpstorm;
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Views\ViewCli;
use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\VarDumper\LightVarDumper;

class TmpException extends \Exception
{
}

/**
 * @method callDynamic($a, $b, $c)
 * @method static TestClass create(array $params)
 */
class TestClass
{
    public function __construct()
    {
        $this->callDynamic(1, 2, 3);
    }

    /**
     * @deprecated
     */
    public function __call($name, $arguments)
    {
        return static::myStaticMethod($this);
    }

    public static function __callStatic($name, $arguments)
    {
        return new static();
    }

    public static function myStaticMethod()
    {
        $clone = new SerializableException(new TmpException('My test exception'));
        $clone->getStackTrace()->setVarDumper(new LightVarDumper());

        if ('cli' === \php_sapi_name()) {
            $view = new ViewCli(7, 3);
            $view->display($clone);
            exit;
        }

        $view = new ViewHtml();
        $view
            ->setEditor(new Phpstorm())
            ->display($clone);
    }
}

\ob_start();
TestClass::create(5);
$result = \ob_get_contents();
\ob_end_clean();

return $result;
