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
set -x

USERNAME='phpseclib'
PASSWORD='EePoov8po1aethu2kied1ne0'

# Create phpseclib user and home directory
sudo useradd --create-home --base-dir /home "$USERNAME"

# Set phpseclib user password
echo "$USERNAME:$PASSWORD" | sudo chpasswd

# Create a 1024 bit RSA SSH key pair without passphrase for the travis user
ssh-keygen -t rsa -b 1024 -f "$HOME/.ssh/id_rsa" -q -N ""

# Add the generated private key to SSH agent of travis user
ssh-add "$HOME/.ssh/id_rsa"

# Allow the private key of the travis user to log in as phpseclib user
sudo mkdir -p "/home/$USERNAME/.ssh/"
sudo cp "$HOME/.ssh/id_rsa.pub" "/home/$USERNAME/.ssh/authorized_keys"
sudo chown "$USERNAME:$USERNAME" "/home/$USERNAME/.ssh/" -R
