# ErrorDumper

[![Coverage Status](https://coveralls.io/repos/github/awesomite/error-dumper/badge.svg?branch=master)](https://coveralls.io/github/awesomite/error-dumper?branch=master)
[![Build Status](https://travis-ci.org/awesomite/error-dumper.svg?branch=master)](https://travis-ci.org/awesomite/error-dumper)

Debugger integrated with PhpStorm.
`ErrorDumper` allows you to catch all kinds of errors and exceptions in PHP.
You will be able to serialize, restore and display them later in readable form.
[@See](https://awesomite.github.io/error-dumper/examples/exception.html) example.

## Table of contents

* [Installation](#installation)
* [How to use?](#how-to-use)
  * [Development environment](#development-environment)
  * [Production environment](#production-environment)
  * [Sandbox](#sandbox)
  * [Integration with PhpStorm](#integration-with-phpstorm)
* [Versioning](#versioning)
* [Examples](#examples)

## Installation

```bash
composer require awesomite/error-dumper
```

## How to use?

### Development environment

```php
use Awesomite\ErrorDumper\DevErrorDumper;

$errorDumper = new DevErrorDumper();
$errorDumper->getErrorHandler()
    ->registerOnException() // uncaught exceptions
    ->registerOnError() // errors @see http://php.net/manual/en/function.trigger-error.php
    ->registerOnShutdown(); // @see http://php.net/manual/en/function.error-get-last.php
```

### Production environment

#### Register handlers

```php
use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Cloners\ClonedException;

$callback = function ($exception) {
    /** @var \Exception|\Throwable $exception */
    $clone = new ClonedException($exception);
    $serialized = serialize($clone);
    // TODO store serialized exception
    // use $clone->getStackTrace()->getId() to count number of occurrences similar errors
    echo '503';
    exit(1);
};
$errorDumper = new ErrorDumper($callback);
$errorDumper->getErrorHandler()
    ->registerOnException() // uncaught exceptions
    ->registerOnError() // errors @see http://php.net/manual/en/function.trigger-error.php
    ->registerOnShutdown(); // @see http://php.net/manual/en/function.error-get-last.php
```

#### Display error in errorlog

```php
// TODO fetch $serialized data from your storage
$unserialized = unserialize($serialized);
$errorDumper->displayHtml($unserialized);
```

### Sandbox

#### Popular case of sandbox usage

```php
$sandbox->executeSafely(function () {
    /** @var \Twig_Environment $twig */
    $twig->render('template.twig');
});
```

#### Full story

PHP supports one error control operator: the at sign @ 
([@see](http://php.net/manual/en/language.operators.errorcontrol.php)).
Try execute (without custom error handler) the following samples of code:

```php
trigger_error('Test error');
echo 'OK';
```

Expected output:

```
Notice: Test error
OK
```

```php
@trigger_error('Test error');
echo 'OK';
```

Expected output:
```
OK
```

Official PHP [documentation](http://php.net/manual/en/language.operators.errorcontrol.php) says:

> If you have set a custom error handler function with set_error_handler() then it will still get called,
but this custom error handler can (and should) call error_reporting()
which will return 0 when the call that triggered the error was preceded by an @.

It means that you will probably need sandbox with disabled error handling.
Error dumper has built-in sandbox mechanism.
The following example shows how to use sandbox:

```php
use Awesomite\ErrorDumper\DevErrorDumper;

// register handlers
$errorDumper = new DevErrorDumper();
$errorHandler = $errorDumper->getErrorHandler();
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();
    
$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test');
});
```

Instead of `executeSafely` you can use `execute`, which will throw exception in case of error.

```php
use Awesomite\ErrorDumper\Sandboxes\SandboxException;

// register handlers
$errorDumper = new DevErrorDumper();
$errorHandler = $errorDumper->getErrorHandler();
$errorHandler
    ->registerOnError()
    ->registerOnException()
    ->registerOnShutdown();

try {
    $sandbox = $errorHandler->getErrorSandbox();
    $sandbox->execute(function () {
        trigger_error('test');
    });
} catch (SandboxException $exception) {
    header('Content-Type: text/plain');
    echo 'Error message: ' . $exception->getMessage() . "\n";
    echo 'Error code: ' . $exception->getCode() . "\n";
    echo 'Location in code: ' . $exception->getFile() . ':' . $exception->getLine();
    exit;
}
```

### Integration with PhpStorm

```php
use Awesomite\ErrorDumper\Views\ViewHtml;
use Awesomite\ErrorDumper\Editors\Phpstorm;

$view = new ViewHtml();
$phpstorm = new Phpstorm();
$view->setEditor($phpstorm);
$view->display($exception);
```

`ViewHtml` has method `setEditor`. It allows you to achieve the following effect:

![Links in stack trace](resources/links.png)

Click on line number and you will be redirected to PhpStorm directly from browser.

## Versioning

The version numbers follow the [Semantic Versioning 2.0.0](http://semver.org/) scheme.

## Examples

To run examples you need at least PHP 5.4.

```bash
composer update --dev
bin/webserver.sh
```

Execute above commands and open in your browser url `http://localhost:8001`.
To run example in terminal, execute `bin/test.php`.