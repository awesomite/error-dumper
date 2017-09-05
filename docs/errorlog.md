# Error log

## Prepare error handler

```php
<?php

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;

$callback = function ($exception) {
    /** @var \Exception|\Throwable $exception */
    $clone = new SerializableException($exception);
    $serialized = serialize($clone);
    // TODO store serialized exception
    // use $clone->getStackTrace()->getId() to count number of occurrences similar errors
    //
    // e.g.:
    //
    // $stackTraceId = $clone->getStackTrace()->getId();
    // if ($errorlog->exists($stackTraceId) {
    //     $errorlog->increment($stackTraceId);
    // } else {
    //     $errorlog->insert($stackTraceId, $serialized);
    // }
    echo '503';
};

$handler = new ErrorHandler();
$handler
    ->pushListener(new OnExceptionCallable($callback))
    ->register();
```

## Display error in error log

```php
<?php

use Awesomite\ErrorDumper\Views\ViewHtml;

$view = new ViewHtml();
// TODO fetch $serialized data from your storage
/** @var string $serialized */
$unserialized = unserialize($serialized);
$view->display($unserialized);
```
