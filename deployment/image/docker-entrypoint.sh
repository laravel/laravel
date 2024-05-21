#/bin/sh
echo "Migrating database\n"
mkdir -vp /var/www/REPLACEPROJECTNAME/storage/logs
php artisan migrate --isolated --force
#php artisan up
chown -R nginx:nginx /var/www/REPLACEPROJECTNAME/storage/
chmod -R 755 /var/www/REPLACEPROJECTNAME/storage/
echo "Restarting fpm\n"
echo "Starting services\n"

echo "start fpm"
/etc/init.d/php8.4-fpm start
# echo "start cron"
# cron
echo "start nginx"
nginx -g "daemon off;"

echo "Nginx ended\n"

exit 1
