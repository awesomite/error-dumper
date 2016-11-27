#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if php -r "var_export(get_loaded_extensions(true));" | grep --quiet -i xdebug; then
  php ${DIR}/../vendor/bin/coveralls -v
else
  echo "Xdebug is not installed"
fi