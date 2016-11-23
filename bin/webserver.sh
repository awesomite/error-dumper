#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
${DIR}/kill-webserver.sh
screen -dm php -S localhost:8001 -t ${DIR}/errordumper_webserver_root
echo "Open http://localhost:8001"
