# Filtering Exceptions

```php
<?php

use Awesomite\ErrorDumper\Handlers\ErrorHandler;
use Awesomite\ErrorDumper\Listeners\OnExceptionCallable;
use Awesomite\ErrorDumper\Listeners\PreExceptionCallable;
use Awesomite\ErrorDumper\Listeners\StopPropagationException;
use Awesomite\ErrorDumper\StandardExceptions\ErrorException;
use Psr\Log\LoggerInterface;

/** @var LoggerInterface $deprecationsLogger */
/** @var LoggerInterface $errorLogger */

/*
 * The following function accepts only instance of ErrorException
 * whenever other error will be thrown, listener will be not called
 */
$deprecationCallback = function (ErrorException $exception) use ($deprecationsLogger) {
    if ($exception->isDeprecated()) {
        $deprecationsLogger->warning($exception->getMessage());

        throw new StopPropagationException();
    }
};

/*
 * The following function accepts all types of exceptions
 */
$realErrorCallback = function ($exception) use ($errorLogger) {
    /** @var \Exception|\Throwable $exception */
    $errorLogger->critical($exception->getMessage());
    echo 'Error';
    exit(1);
};

$handler = new ErrorHandler();
$handler

    /*
     * Prelisteners will be called before normal listeners,
     * in prelistener you can stop propagate next listeners
     */
    ->pushPreListener(new PreExceptionCallable($deprecationCallback))
    
    /*
     * Event called whenever exception on error is thrown
     */
    ->pushListener(new OnExceptionCallable($realErrorCallback))
    
    /*
     * Do not call `exit(1)` after trigger listeners
     */
    ->exitAfterTrigger(false)
    
    /*
     * Register all handlers
     */
    ->register();
```
