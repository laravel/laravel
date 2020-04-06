# Install cron
apt-get update -qq && apt-get install cron -yqq
service cron start

PHP=`which php`

(crontab -l 2>/dev/null; echo "* * * * * php /home/site/wwwroot/artisan schedule:run")|crontab
(crontab -l 2>/dev/null; echo "* * * * * /home/site/wwwroot/.azure/test.sh")|crontab
