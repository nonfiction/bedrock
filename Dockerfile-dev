FROM node:latest

WORKDIR /srv/

RUN set -ex; \
  npm install -g \
    webpack \
    webpack-cli \
    webpack-dev-server \
    eslint \
    stylelint \
  ;

EXPOSE 80

CMD ["webpack"]