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

COPY ./dev /srv/dev

WORKDIR /srv/web/app/site/

CMD ["webpack"]
