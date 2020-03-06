#!/bin/bash

# Generate WP salts and save as an .env file
curl https://api.wordpress.org/secret-key/1.1/salt/ | tr -d "[:blank:]" > /tmp/salts
sed -e "s/define('//g" -e "s/','/='/g" -e "s/);//g" /tmp/salts > /srv/salts.env

# # Put dist themes where expected
# rm -rf /srv/web/app/themes 
# ln -sf /srv/web/wp/wp-content/themes /srv/web/app/themes

# Run Apache in foregrond
apachectl -D FOREGROUND
