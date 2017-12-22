#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if php -r "var_export(get_loaded_extensions(true));" | grep --quiet -i xdebug; then
  if [ -f ${DIR}/../dev-tools/vendor/bin/coveralls ]; then
    php ${PHP_ARGS} ${DIR}/../dev-tools/vendor/bin/coveralls -v
  else
    php ${PHP_ARGS} ${DIR}/../dev-tools/vendor/bin/php-coveralls -v
  fi
else
  echo "Xdebug is not installed"
fi
