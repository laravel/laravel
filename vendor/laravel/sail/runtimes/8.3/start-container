#!/usr/bin/env bash

if [ "$SUPERVISOR_PHP_USER" != "root" ] && [ "$SUPERVISOR_PHP_USER" != "sail" ]; then
    echo "You should set SUPERVISOR_PHP_USER to either 'sail' or 'root'."
    exit 1
fi

if [ ! -z "$WWWUSER" ]; then
    usermod -u $WWWUSER sail
fi

if [ ! -d /.composer ]; then
    mkdir /.composer
fi

chmod -R ugo+rw /.composer

if [ $# -gt 0 ]; then
    if [ "$SUPERVISOR_PHP_USER" = "root" ]; then
        exec "$@"
    else
        exec gosu $WWWUSER "$@"
    fi
else
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
