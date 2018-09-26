# Quick start

## Installation

```bash
composer install awesomite/error-dumper
```

## Integration with your application (only debug mode)

`index.php`:
```php
<?php

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Views\ViewFactory;

/*
 * Skip E_DEPRECATED errors
 */
$mode = E_ALL & ~E_DEPRECATED;

error_reporting($mode);
$errorHandler = new ErrorHandler($mode);
$errorHandler->pushListener(new OnExceptionDevView(ViewFactory::create()));
$errorHandler->register();
```

## Integration with your application

`index.php`:
```php
<?php

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;
use Awesomite\ErrorDumper\Listeners\OnExceptionDevView;
use Awesomite\ErrorDumper\Serializable\SerializableException;
use Awesomite\ErrorDumper\Views\ViewFactory;
use Awesomite\ErrorDumper\Views\ViewHtml;

/** @var bool $inDebugMode */

/*
 * Skip E_DEPRECATED_ERRORS
 */
$mode = E_ALL & ~E_DEPRECATED;

\error_reporting($mode);
$errorHandler = new ErrorHandler($mode);

if ($inDebugMode) {
    $errorHandler->pushListener(new OnExceptionDevView(ViewFactory::create()));
} else {
    $errorHandler->pushListener(new OnExceptionCallable(function ($exception) {
        /**
         * Serialize and save exception somewhere:
         *
         * $serialized = serialize(new SerializableException($exception));
         *
         * Later you can unserialize and display exception in readable format:
         *
         * $view = new ViewHtml();
         * $view->display(unserialize($serialized));
         */

        echo 'Error 503';
        exit;
    }));
}

$errorHandler->register();

```
