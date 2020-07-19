FROM nonfiction/bedrock:srv

# Persist uploads in this volume
VOLUME /srv/web/app/uploads

# Give Apache permissions for /app dir
RUN chown -R www-data:www-data /srv/web/app

# Add bash script
COPY --chown=www-data:www-data ./run.sh /srv/web/app/run.sh

# Copy the site's composer.json for install
COPY --chown=www-data:www-data ./site/composer.json /srv/web/app/site/composer.json

# Install all PHP packages including Wordpress
RUN composer update -d /srv
RUN composer dump-autoload -o -d /srv

# Copy the full codebase
COPY --chown=www-data:www-data ./site /srv/web/app/site
