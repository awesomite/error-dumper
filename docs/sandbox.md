# Sandbox

## Popular case of sandbox usage

```php
<?php

$sandbox->executeSafely(function () {
    /** @var \Twig_Environment $twig */
    $twig->render('template.twig');
});
```

## Full story

PHP supports one error control operator: the at sign @ 
([@see](http://php.net/manual/en/language.operators.errorcontrol.php)).
Try execute (without custom error handler) the following samples of code:

```php
<?php

trigger_error('Test error');
echo 'OK';
```

Expected output:

```
Notice: Test error
OK
```

```php
<?php

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

It means there are two ways depend on your error_reporting settings:

### error_reporting(E_ALL | E_STRICT)

```php
<?php

error_reporting(E_ALL | E_STRICT);

set_error_handler(function ($code, $message, $file, $line) {
    if ($code & error_reporting()) {
        echo 'ERROR: ' . $message;
        exit;
    }
});

@trigger_error('Test'); // will do nothing
```

### error_reporting(0)

```php
<?php

error_reporting(0);

set_error_handler(function ($code, $message, $file, $line) {
    echo 'ERROR: ' . $message;
    exit;
});

@trigger_error('Test'); // will display "ERROR: Test" and will stop script
```

If you have `error_reporting(0)` you can need sandbox for errors:

```php
<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;

$errorHandler = ErrorDumper::createDevHandler(null, ErrorHandler::POLICY_ALL);
$errorHandler->register();

$sandbox = $errorHandler->getErrorSandbox();
$sandbox->executeSafely(function () {
    trigger_error('test'); // will do nothing
});
```

Instead of `executeSafely` you can use `execute`, which will throw exception in case of error.

```php
<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Sandboxes\SandboxException;

$errorHandler = ErrorDumper::createDevHandler();
$errorHandler->register();

try {
    $sandbox = $errorHandler->getErrorSandbox();
    $sandbox->execute(function () {
        trigger_error('test'); // will throw SandboxException
    });
} catch (SandboxException $exception) {
    header('Content-Type: text/plain');
    echo 'Error message: ' . $exception->getMessage() . "\n";
    echo 'Severity: ' . $exception->getSeverity() . "\n";
    echo 'Location in code: ' . $exception->getFile() . ':' . $exception->getLine();
    exit;
}
```
