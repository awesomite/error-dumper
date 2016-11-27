<?php

class TmpException extends \Exception
{
}

/**
 * Class TestClass
 * @method callDynamic($a, $b, $c)
 * @method static TestClass create(array $params)
 */
class TestClass
{
    public function __construct()
    {
        $this->callDynamic(1, 2, 3);
    }

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
        $clone = new \Awesomite\ErrorDumper\Cloners\ClonedException(new TmpException('My test exception'));
        $clone->getStackTrace()->setVarDumper(new \Awesomite\StackTrace\VarDumpers\LightVarDumper());

        if (php_sapi_name() === 'cli') {
            $view = new \Awesomite\ErrorDumper\Views\ViewCli(7, 3);
            $view->display($clone);
            exit;
        }

        $view = new \Awesomite\ErrorDumper\Views\ViewHtml();
        $view->setEditor(new \Awesomite\ErrorDumper\Editors\Phpstorm());
        $view->display($clone);
    }
}


ob_start();
TestClass::create(5);
$result = ob_get_contents();
ob_end_clean();

return $result;