# Install cron
apt-get update -qq && apt-get install cron -yqq
service cron start

cd /home/site/wwwroot

(crontab -l 2>/dev/null; echo "* * * * * cd /home/site/wwwroot;rm -rf github*;touch github_$( date '+%Y-%m-%d_%H-%M-%S' ).txt")|crontab
