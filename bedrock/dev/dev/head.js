// This is the <head> entry file for webpack--do not edit!

import '/srv/web/app/site/assets/head.js';
// import '/srv/dev/posts-bundle.js';

if (module.hot) {
  module.hot.accept( '/srv/web/app/site/assets/head.js' );
  // module.hot.accept( '/srv/dev/posts-bundle.js' );
}
