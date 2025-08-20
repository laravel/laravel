#!/bin/bash
set -e
sudo systemctl reload nginx || sudo systemctl restart nginx
sudo systemctl restart php-fpm