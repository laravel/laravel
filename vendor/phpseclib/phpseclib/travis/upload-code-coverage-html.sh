#!/bin/sh
#
# This file is part of the phpseclib project.
#
# (c) Andreas Fischer <bantu@phpbb.com>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

USERNAME='phpseclib'
HOSTNAME='phpseclib.bantux.org'
HOSTRSAF='09:40:96:14:6a:cd:67:46:17:e5:b4:39:24:24:6e:9d'
LDIRNAME='code_coverage'
RDIRNAME='code_coverage'
ID_RSA='travis/code_coverage_id_rsa'

# Install expect if necessary
if ! which expect > /dev/null
then
	sudo apt-get update -qq
	sudo apt-get install -qq expect
fi

# Workaround for rsync not creating target directories with depth > 1
mv "$LDIRNAME" "x$LDIRNAME"
RROOT="$RDIRNAME/$TRAVIS_BRANCH/$TRAVIS_BUILD_NUMBER"
mkdir -p "$RROOT"
mv "x$LDIRNAME" "$RROOT/PHP-$TRAVIS_PHP_VERSION/"

# Update latest symlink
ln -s "$TRAVIS_BUILD_NUMBER" "$RDIRNAME/$TRAVIS_BRANCH/latest"

# Stop complaints about world-readable key file.
chmod 600 "$ID_RSA"

export RSYNC_RSH="ssh -4 -i $ID_RSA -o ConnectTimeout=5"
RSYNC_OPT="--recursive --times --links --progress"

expect << EOF
	spawn rsync $RSYNC_OPT "$RDIRNAME/" "$USERNAME@$HOSTNAME:$RDIRNAME/"

	expect "RSA key fingerprint is $HOSTRSAF."
	send "yes\n"

	expect "Enter passphrase for key '$ID_RSA':"
	send "$CODE_COVERAGE_PASSPHRASE\n"

	expect eof
EOF
