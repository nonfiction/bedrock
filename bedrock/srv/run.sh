#!/bin/bash

# Symlink all plugins and themes to where they belong
for f in /srv/web/app/site/mu-plugins/*; do [ -e $f ] && ln -sf $f /srv/web/app/mu-plugins/; done
for f in /srv/web/app/site/plugins/*;    do [ -e $f ] && ln -sf $f /srv/web/app/plugins/;    done
for f in /srv/web/app/site/themes/*;     do [ -e $f ] && ln -sf $f /srv/web/app/themes/;     done

# Extract any zip archives
for f in /srv/web/app/mu-plugins/*.zip; do [ -e $f ] && unzip $f; done
for f in /srv/web/app/plugins/*.zip;    do [ -e $f ] && unzip $f; done
for f in /srv/web/app/themes/*.zip;     do [ -e $f ] && unzip $f; done

# Run wp-cron every 5 minutes
echo "*/5 * * * * root curl https://$APP_NAME.$APP_HOST/wp/wp-cron.php?doing_wp_cron > /dev/null 2>&1" >> /etc/crontab
service cron start

# Execute run.sh if it exists
if [ -e /srv/web/app/run.sh ]; then
  chmod +x /srv/web/app/run.sh
  /srv/web/app/run.sh
fi

# Run Apache in foregrond
apachectl -D FOREGROUND
