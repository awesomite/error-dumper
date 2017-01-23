# Changelog

## 0.8.1

* Added method `Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface::register`

## 0.8.0

* Added methods `enableCaching` and `disableCaching` to `\Awesomite\ErrorDumper\Views\ViewHtml`.
Caching is disabled as default.
* Improved recognizing root path of templates - it didn't work when `vendor` was located in `src` directory.
* Strikethrough on deprecated functions.
* Updated `awesomite/stack-trace` to `^0.6.0`

## 0.7.0

* `\Awesomite\ErrorDumper\Views\ViewHtml` sends proper http headers
* `\Awesomite\ErrorDumper\Handlers\ErrorHandler` terminates application in default settings when is triggered

## 0.6.1

* Fixed bug in `\Awesomite\ErrorDumper\Handlers\ErrorHandler::handleError` - POLICY_ALL didn't work properly
* Updated `awesomite/stack-trace` to `^0.5.1`

## 0.6.0

Version `0.6.0` is **incompatible** with `0.5.0`.

Backward compatible changes:
* Added constants `HANDLER_*` in `\Awesomite\ErrorDumper\Handlers\ErrorHandler` class
* Added methods `handleError`, `handleException`, `handleShutdown`, `pushListener` and `pushValidator`
to `\Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface` and `\Awesomite\ErrorDumper\Handlers\ErrorHandler`

Backward incompatible changes:
* Constructor of `\Awesomite\ErrorDumper\Handlers\ErrorHandler` has been changed
* The following classes have been removed:
  * `\Awesomite\ErrorDumper\ErrorDumperInterface`
  * `\Awesomite\ErrorDumper\DevErrorDumper`
  * `\Awesomite\ErrorDumper\AbstractErrorDumper`
* Class `\Awesomite\ErrorDumper\ErrorDumper` is fully rewritten, contains only one method - `createDevHandler`
* Changed constructor of `\Awesomite\ErrorDumper\Handlers\ErrorHandler` - argument `$event` has been removed,
argument `$policy` has been added.
Default `$policy` is `ErrorHandler::POLICY_ERROR_REPORTING`.
`ErrorHandler::POLICY_ALL` is equivalent to behaviour as in previous version.
* Class `Awesomite\ErrorDumper\StandardExceptions\FatalErrorException` has been removed.
`ErrorHandler::handleError` creates `ErrorException` instead of `FatalErrorException`.
* Updated `awesomite/stack-trace` to `^0.5.0`

## 0.5.1

* Updated `awesomite/stack-trace` to version `^0.4.0`

## 0.5.0

* Updated `awesomite/stack-trace` to version `^0.3.2` (improvements in `LightVarDumper`)

## 0.4.0

* Added optional parameter `$withPrevious` in `ClonedException::__construct()`

## 0.3.0

* Methods `execute` and `executeSafely` in `ErrorSandboxInterface` will return value of result of passed callback
* Backward incompatible changes for `ShutdownErrorException`, `FatalErrorException` and `SandboxException`
(setters have been removed, constructor and parent class have been changed)

## 0.2.0

* Limit for number of displayed steps in CLI
* Fixed bug in `ErrorSandbox` - exception should be thrown immediately after error

## 0.1.0

* Initial public release