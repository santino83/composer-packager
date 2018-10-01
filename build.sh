#!/usr/bin/env bash

## get script directory
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
DIR="$( cd -P "$( dirname "$SOURCE" )" >/dev/null && pwd )"

PHP_EXEC=$(which php)
PHAR="$DIR/tools/phar-builder.phar"
CMD_OPTIONS="-d phar.readonly=0"
COMPOSER_JSON="$DIR/composer.json"
PHAR_CMD="package"

exec $PHP_EXEC $CMD_OPTIONS $PHAR $PHAR_CMD $COMPOSER_JSON