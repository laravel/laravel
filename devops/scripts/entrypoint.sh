#!/bin/bash

echo "env: ${DYNO}"
echo "port: ${PORT}"

envsubst "\$PORT" < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

if [ -z "${DYNO}" ]; then
    echo "Running web application locally"
    php artisan migrate
    php artisan migrate --env=testing

    echo "Running supervisor"
    /usr/bin/supervisord -c /etc/supervisord.conf
elif [[ ${DYNO} == "worker."* ]]; then
    echo "Running application: ${DYNO}"

    php artisan optimize:clear
    php artisan optimize

    echo "Running queue:work"
    php artisan queue:work --tries=3 --memory=512 --timeout=3600
else
    echo "Running without application: ${DYNO}"
    php artisan migrate --force
    php artisan optimize:clear
    php artisan optimize

    echo "Running supervisor"
    /usr/bin/supervisord -c /etc/supervisord.conf
fi
