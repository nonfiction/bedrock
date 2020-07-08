#!/bin/bash

# Generate WP salts and save as an .env file
curl https://api.wordpress.org/secret-key/1.1/salt/ | tr -d "[:blank:]" > /tmp/salts
sed -e "s/define('//g" -e "s/','/='/g" -e "s/);//g" /tmp/salts > /srv/salts.env

# Run wp-cron every 5 minutes
echo "*/5 * * * * root curl https://$APP_NAME.$APP_HOST/wp-cron.php?doing_wp_cron > /dev/null 2>&1" >> /etc/crontab

# Execute run.sh if it exists
if [ -e /srv/web/app/run.sh ]; then
  chmod +x /srv/web/app/run.sh
  /srv/web/app/run.sh
fi

# Run Apache in foregrond
apachectl -D FOREGROUND
