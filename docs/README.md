# Error Dumper Documentation

## Table of contents

* [Quick start](#quick-start)
* [Error preview](#error-preview)
* [Filtering exceptions by class](#filtering-exceptions-by-class)
* [Error log](#error-log)
* [Sandbox](#sandbox)
* [IDE integration](#ide-integration)

## Quick start

Read [quick-start.md](quick-start.md)

## Error preview

Read [preview.md](preview.md)

## Filtering exceptions by class

Error dumper allows you to define different handlers for different exceptions classes.

```php
<?php

use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;

// ...

$onRuntimeException = function (\RuntimeException $exception) {
    // do something
};

$onErrorException = function (\ErrorException $exception) {
    // do something
};

/** @var \Awesomite\ErrorDumper\Handlers\ErrorHandler $handler */
$handler
    ->pushListener(new OnExceptionCallable($onRuntimeException))
    ->pushListener(new OnExceptionCallable($onErrorException));
```

Read [filtering-exceptions.md](filtering-exceptions.md)


## Error log

Error Dumper allows for serialization exceptions (for restoring them later).
Using this feature you can build own error log.

Read [errorlog.md](errorlog.md)

## Sandbox

Run piece of code outside error handler.

Read [sandbox.md](sandbox.md)

## IDE integration

Read [ide-integration](ide-integration.md)
