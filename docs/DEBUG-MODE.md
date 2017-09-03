# Debug mode

```php
<?php

use Awesomite\ErrorDumper\ErrorDumper;

/** @var bool $isDebugMode */

if ($isDebugMode) {
    $mode = (E_ALL | E_NOTICE) & ~(E_DEPRECATED | E_USER_DEPRECATED);
    $handler = ErrorDumper::createDevHandler($mode)->register();
}
```

## Preview formats

### HTML

[Preview](https://awesomite.github.io/error-dumper/examples/exception.html)

![Exception displayed as HTML](resources/exception-html.png)

### CLI

![Exception displayed in terminal](resources/exception-cli.png)

```bash
git clone https://github.com/awesomite/error-dumper
cd error-dumper
composer update --dev
php bin/test.php
```
