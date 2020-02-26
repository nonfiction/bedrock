#!/bin/bash

# Generate WP salts and save as an .env file
curl https://api.wordpress.org/secret-key/1.1/salt/ | tr -d "[:blank:]" > /tmp/salts
sed -e "s/define('//g" -e "s/','/='/g" -e "s/);//g" /tmp/salts > /srv/.env-salts

# Run Apache in foregrond
apachectl -D FOREGROUND
