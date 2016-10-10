#!/bin/bash
#
# This file is part of the phpseclib project.
#
# (c) Andreas Fischer <bantu@phpbb.com>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
set -e

function install_php_extension
{
    cd "$1"
    phpize
    ./configure
    make
    make install
    cd ..
    echo "extension=$1.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
}

# runkit
if [ "$TRAVIS_PHP_VERSION" == "5.6" ]
then
    git clone https://github.com/adrianguenter/runkit.git
else
    git clone https://github.com/zenovich/runkit.git
fi
install_php_extension 'runkit'
