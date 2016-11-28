# Changelog

## 0.3

* Methods `execute` and `executeSafely` in `ErrorSandboxInterface` will return value of result of passed callback
* Backward incompatible changes for ShutdownErrorException, FatalErrorException and SandboxException
(setters have been removed, constructor and parent class have been changed)

## 0.2

* Limit for number of displayed steps in CLI
* Fixed bug in error sandbox - exception should be thrown immediately after error

## 0.1

* Initial public release