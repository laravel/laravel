#!/bin/sh
set -e
set -x

export PHPSECLIB_SSH_HOSTNAME='localhost'
export PHPSECLIB_SSH_USERNAME='phpseclib'
export PHPSECLIB_SSH_PASSWORD='EePoov8po1aethu2kied1ne0'
export PHPSECLIB_SSH_HOME='/home/phpseclib'

if [ "$TRAVIS_PHP_VERSION" = '5.2' ]
then
  PHPUNIT="phpunit"
else
  PHPUNIT="$(dirname "$0")/../vendor/bin/phpunit"
fi

PHPUNIT_ARGS='--verbose'
if [ `php -r "echo (int) version_compare(PHP_VERSION, '5.4', '<');"` = "1" ]
then
  PHPUNIT_ARGS="$PHPUNIT_ARGS -d zend.enable_gc=0"
fi

if [ "$TRAVIS_PHP_VERSION" = 'hhvm' ]
then
  find tests -type f -name "*Test.php" | \
    parallel --gnu --keep-order \
      "echo '== {} =='; \"$PHPUNIT\" $PHPUNIT_ARGS {};"
else
  "$PHPUNIT" \
    $PHPUNIT_ARGS \
    --coverage-text \
    --coverage-clover code_coverage/clover.xml \
    --coverage-html code_coverage/
fi
