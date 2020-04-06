# Install cron
apt-get update -qq && apt-get install cron -yqq
service cron start

cd /home/site/wwwroot

rm -rf foo*
touch foo_$( date '+%Y-%m-%d_%H-%M-%S' ).txt
