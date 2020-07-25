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

RUN ln -sf /srv/web/app/site/package.json /srv/package.json && \
    ln -sf /srv/web/app/site/package-lock.json /srv/package-lock.json && \
    ln -sf /srv/web/app/site/webpack.config.js /srv/webpack.config.js

WORKDIR /srv/

CMD ["webpack"]
