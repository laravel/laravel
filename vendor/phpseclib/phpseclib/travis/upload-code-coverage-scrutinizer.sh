#!/bin/sh
#
# This file is part of the phpseclib project.
#
# (c) Andreas Fischer <bantu@phpbb.com>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
set -e

wget https://scrutinizer-ci.com/ocular.phar
php ocular.phar code-coverage:upload --format=php-clover code_coverage/clover.xml
