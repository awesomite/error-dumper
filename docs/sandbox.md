# Sandbox

## Popular case of sandbox usage

```php
<?php

$sandbox->executeSafely(function () {
    return require 'data.cache';
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

It means that we should check type of error inside out error handler for each time:

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

Instead of using `@` operator you can use built-in sandbox mechanism

```php
<?php

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;

$errorHandler = new ErrorHandler();

$sandbox = $errorHandler->getErrorSandbox(); // or new ErrorSandbox(/* optional $errorTypes */);
$sandbox->executeSafely(function () {
    trigger_error('test'); // will do nothing
});
```

Instead of `executeSafely` you can use `execute`, which will throw exception in case of error.

```php
<?php

use Awesomite\ErrorDumper\Sandboxes\ErrorSandbox;
use Awesomite\ErrorDumper\Sandboxes\SandboxException;

$sandbox = new ErrorSandbox();

try {
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
