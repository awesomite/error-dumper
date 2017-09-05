# Quick start

## Installation

```bash
composer install awesomite/error-dumper
```

## Integration with your application (only debug mode)

`web/index.php`:
```php
<?php

use Awesomite\ErrorDumper\ErrorDumper;

/** @var bool $inDebugMode */

/*
 * Skip E_DEPRECATED_ERRORS
 */
$mode = E_ALL & ~E_DEPRECATED;

ErrorDumper::createDevHandler($mode)->register();
```

## Integration with your application

`web/index.php`:
```php
<?php

use Awesomite\ErrorDumper\ErrorDumper;
use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;

/** @var bool $inDebugMode */

/*
 * Skip E_DEPRECATED_ERRORS
 */
$mode = E_ALL & ~E_DEPRECATED;

if ($inDebugMode) {
    $handler = ErrorDumper::createDevHandler($mode);
} else {
    $handler = new ErrorHandler($mode);
    $handler->pushListener(new OnExceptionCallable(function ($exception) {
        /** @var \Exception|\Throwable $exception */
        // log somewhere $exception
        echo 'Error 503';
        exit;
    }));
}

$handler->register();
```
