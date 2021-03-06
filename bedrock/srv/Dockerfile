# https://hub.docker.com/_/php/
# https://github.com/docker-library/php
FROM php:7.4-apache

# Install packages we need for WordPress
RUN set -ex; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    cron \
    ghostscript \
    git-core \
    less \
    libfreetype6-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    zlib1g-dev \
  ;

# https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions
RUN set -ex; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  docker-php-ext-install -j "$(nproc)" \
    bcmath \
    exif \
    gd \
    mysqli \
    opcache \
    zip \
  ; \
  pecl install imagick-3.4.4; \
  docker-php-ext-enable imagick;

# Clean up
RUN set -ex; \
  apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
  rm -rf /var/lib/apt/lists/*

# Customize PHP config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./config/php-custom.ini $PHP_INI_DIR/conf.d/php-custom.ini

# Enable Apache modules
RUN set -ex; a2enmod \
  cache \
  deflate \
  expires \
  headers \
  proxy \
  proxy_ajp \
  proxy_balancer \
  proxy_connect \
  proxy_http \
  rewrite \
  ssl \
  ;

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Properly log IP addresses when behind proxy
# https://github.com/docker-library/wordpress/issues/383
RUN set -ex; \
  a2enmod remoteip; { \
    echo 'RemoteIPHeader X-Forwarded-For'; \
    echo 'RemoteIPTrustedProxy 10.0.0.0/8'; \
    echo 'RemoteIPTrustedProxy 172.16.0.0/12'; \
    echo 'RemoteIPTrustedProxy 192.168.0.0/16'; \
    echo 'RemoteIPTrustedProxy 169.254.0.0/16'; \
    echo 'RemoteIPTrustedProxy 127.0.0.0/8'; \
  } > /etc/apache2/conf-available/remoteip.conf; \
  a2enconf remoteip; \
  find /etc/apache2 -type f -name '*.conf' -exec sed -ri 's/([[:space:]]*LogFormat[[:space:]]+"[^"]*)%h([^"]*")/\1%a\2/g' '{}' +

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY ./composer.json /srv/composer.json

# WP-CLI
RUN set -ex; \
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
  chmod +x wp-cli.phar; \
  mv wp-cli.phar /usr/local/bin/wp-cli.phar; \
  echo '#!/bin/sh' >> /usr/local/bin/wp; \
  echo 'wp-cli.phar "$@" --allow-root' >> /usr/local/bin/wp; \
  chmod +x /usr/local/bin/wp;
COPY ./wp-cli.yml /srv/wp-cli.yml

# Self-signed certificate for https
RUN openssl req -x509 -batch -nodes -days 36525 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt

# Copy the codebase
COPY ./web /srv/web
COPY ./config /srv/config

# Symlink the apache vhost to where it will be found
RUN ln -sf /srv/config/vhost.conf /etc/apache2/sites-available/000-default.conf

# Persist uploads in this volume
VOLUME /srv/web/app/uploads

# Give Apache permissions for /app dir
RUN chown -R www-data:www-data /srv/web/app

# This script starts Apache Server
COPY ./run.sh /bin/run
RUN chmod +x /bin/run
CMD ["/bin/run"]

WORKDIR /srv/web/app/site/
