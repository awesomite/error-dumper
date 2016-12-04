# Changelog

## 0.5.0

* Updated `awesomite/stack-trace` to version `0.3.2` (improvements in `LightVarDumper`)

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