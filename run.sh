#!/bin/bash

# Generate WP salts and save as an .env file
curl https://api.wordpress.org/secret-key/1.1/salt/ | tr -d "[:blank:]" > /tmp/salts
sed -e "s/define('//g" -e "s/','/='/g" -e "s/);//g" /tmp/salts > /srv/salts.env

# Execute run.sh if it exists
if [ -e /srv/web/app/run.sh ]; then
  chmod +x /srv/web/app/run.sh
  /srv/web/app/run.sh
fi

# Run Apache in foregrond
apachectl -D FOREGROUND
