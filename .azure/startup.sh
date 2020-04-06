# Variables
PHP=`which php`
BASEPATH="/home/site/wwwroot"
ARTISAN="$BASEPATH/artisan"

# Setup cron
apt-get update -qq && apt-get install cron -yqq
service cron start

# Cron jobs
(crontab -l 2>/dev/null; echo "* * * * * $PHP $ARTISAN schedule:run")|crontab
