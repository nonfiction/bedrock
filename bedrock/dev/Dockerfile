FROM node:latest

RUN set -ex; \
  npm install -g \
    webpack \
    webpack-cli \
    webpack-dev-server \
    eslint \
    stylelint \
  ;

EXPOSE 80

WORKDIR /srv/web/app/site

COPY ./web/app/site/src/webpack /srv/web/app/site/src/webpack
COPY ./web/app/site/webpack.config.js /srv/web/app/site/webpack.config.js

CMD ["webpack"]
