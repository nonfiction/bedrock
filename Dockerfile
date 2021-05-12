FROM nonfiction/bedrock:srv

# Add bash script
COPY ./run.sh /srv/web/app/run.sh

# Copy the site's composer.json for install
COPY ./site/composer.json /srv/web/app/site/composer.json

# Install all PHP packages including Wordpress
RUN composer update -d /srv
RUN composer dump-autoload -o -d /srv

# Copy the full codebase
COPY --chown=www-data:www-data ./site /srv/web/app/site

# Copy the pagespeed configuration
COPY ./pagespeed.conf /etc/apache2/mods-available/pagespeed.conf
