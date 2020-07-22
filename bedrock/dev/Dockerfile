FROM node:latest

RUN set -ex; \
  npm install -g \
    webpack \
    webpack-cli \
    webpack-dev-server \
    eslint \
    stylelint \
  ;

ENV NODE_PATH=/srv/node_modules

COPY ./dev /srv/dev

WORKDIR /srv/web/app/site/

CMD ["webpack"]
