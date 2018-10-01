#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PHP_ARGS=$(php -r "echo '-d error_reporting=', E_ALL ^ (E_DEPRECATED | E_USER_DEPRECATED);")

php ${PHP_ARGS} ${DIR}/../vendor/bin/phpunit
