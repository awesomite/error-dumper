# Changelog

## 0.14.0 (???)

Versions `0.14.*` are **incompatible** with `0.13.*`.

* Renamed:
  * `Awesomite\ErrorDumper\Cloners\ClonedException` to `Awesomite\ErrorDumper\Serializable\SerializableException`
  * `Awesomite\ErrorDumper\Cloners\ClonedExceptionInterface` to `Awesomite\ErrorDumper\Serializable\SerializableExceptionInterface`
  * `Awesomite\ErrorDumper\Handlers\ErrorHandler::pushValidator` to `Awesomite\ErrorDumper\Handlers\ErrorHandler::pushPreListener`
  * `Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface::pushValidator` to `Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface::pushPreListener`
  * `Awesomite\ErrorDumper\Listeners\ListenerClosure` to `Awesomite\ErrorDumper\Listeners\OnExceptionCallable`
  * `Awesomite\ErrorDumper\Listeners\ListenerDevView` to `Awesomite\ErrorDumper\Listeners\OnExceptionDevView`
  * `Awesomite\ErrorDumper\Listeners\ListenerInterface` to `Awesomite\ErrorDumper\Listeners\OnExceptionInterface`
  * `Awesomite\ErrorDumper\Listeners\ValidatorClosure` to `Awesomite\ErrorDumper\Listeners\PreExceptionCallable`
  * `Awesomite\ErrorDumper\Listeners\ValidatorInterface` to `Awesomite\ErrorDumper\Listeners\PreExceptionInterface`
* Function `Awesomite\ErrorDumper\ErrorDumper::createDevHandler` became static
* Classes `Awesomite\ErrorDumper\StandardExceptions\ErrorException`
and `Awesomite\ErrorDumper\StandardExceptions\ShutdownErrorException` are not internal anymore

## 0.13.7 (2018-09-21)

* Fixed bug in `Awesomite\ErrorDumper\Cloners\ClonedException::__constructor`
- to next exception in chain should be passed the same parameters

## 0.13.6 (2017-12-22)

* Updated `awesomite/var-dumper` to version `^0.6.3 || ^0.7.2 || ^0.8.0 || ^0.9.0`

## 0.13.5 (2017-08-31)

* Updated `awesomite/var-dumper` to version `^0.6.3 || ^0.7.2 || ^0.8.0`

## 0.13.4 (2017-06-06)

 * Updated version of `awesomite/var-dumper` to`^0.6.2 || ^0.7.0`

## 0.13.3 (2017-06-06)

* Updated version of `awesomite/stack-trace` to `^0.9.0 || ^0.10.0`

## 0.13.2 (2017-05-05)

* Added `integrity` attribute for `link` and `script` tags
* Added `.gitattributes` file

## 0.13.1 (2017-05-05)

* Updated dependencies:
  * `awesomite/stack-trace` to `^0.9.0`
  * `awesomite/var-dumper` to `^0.6.1`

## 0.13.0 (2017-04-11)

* Added functions:
  * `Awesomite\ErrorDumper\Views\ViewHtml::disableHeaders`
  * `Awesomite\ErrorDumper\Views\ViewHtml::enableHeaders`
* Added possibility to use chain syntax in `Awesomite\ErrorDumper\Views\ViewHtml`

## 0.12.1 (2017-03-15)

* Fixed bug in `Awesomite\ErrorDumper\Editors\Phpstorm::registerPathMapping` - method should return `$this` instead of `void`.

## 0.12.0 (2017-03-14)

* Parent of `Awesomite\ErrorDumper\StandardExceptions\ErrorException` has been changed to `ErrorException`
* Added method `Awesomite\ErrorDumper\Views\ViewHtml::appendToBody`

## 0.11.1 (2017-02-03)

* Fixed tests for PHP `7.2.0-dev`: `PHP Deprecated:  The each() function is deprecated.`

## 0.11.0 (2017-02-02)

* `Awesomite\ErrorDumper\Listeners\ValidatorInterface::stopPropagation` is removed
* `Awesomite\ErrorDumper\Listeners\ValidatorClosure::stopPropagation` has been static

## 0.10.1

* Updated `awesomite/var-dumper` to `^0.3.0`

## 0.10.0 (2017-01-31)

* Class `Awesomite\ErrorDumper\TestListener` has been internal

## 0.9.0 (2017-01-23)

* Added method `Awesomite\ErrorDumper\Handlers\ErrorHandlerInterface::register`

## 0.8.0 (2017-01-16)

* Added methods `enableCaching` and `disableCaching` to `\Awesomite\ErrorDumper\Views\ViewHtml`.
Caching is disabled as default.
* Improved recognizing root path of templates - it didn't work when `vendor` was located in `src` directory.
* Strikethrough on deprecated functions.
* Updated `awesomite/stack-trace` to `^0.6.0`

## 0.7.0 (2017-01-11)

* `\Awesomite\ErrorDumper\Views\ViewHtml` sends proper http headers
* `\Awesomite\ErrorDumper\Handlers\ErrorHandler` terminates application in default settings when is triggered

## 0.6.1 (2017-01-11)

* Fixed bug in `\Awesomite\ErrorDumper\Handlers\ErrorHandler::handleError` - POLICY_ALL didn't work properly
* Updated `awesomite/stack-trace` to `^0.5.1`

## 0.6.0 (2017-01-10)

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

## 0.5.1 (2016-12-12)

* Updated `awesomite/stack-trace` to version `^0.4.0`

## 0.5.0 (2016-12-05)

* Updated `awesomite/stack-trace` to version `^0.3.2` (improvements in `LightVarDumper`)

## 0.4.0 (2016-11-30)

* Added optional parameter `$withPrevious` in `ClonedException::__construct()`

## 0.3.0 (2016-11-28)

* Methods `execute` and `executeSafely` in `ErrorSandboxInterface` will return value of result of passed callback
* Backward incompatible changes for `ShutdownErrorException`, `FatalErrorException` and `SandboxException`
(setters have been removed, constructor and parent class have been changed)

## 0.2.0 (2016-11-27)

* Limit for number of displayed steps in CLI
* Fixed bug in `ErrorSandbox` - exception should be thrown immediately after error

## 0.1.0 (2016-11-23)

* Initial public release
