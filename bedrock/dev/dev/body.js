// This is the <body> entry file for webpack--do not edit!

import '/srv/web/app/site/src/assets/body.js';
import '/srv/dev/posts-bundle.js';

if (module.hot) {
  module.hot.accept( '/srv/web/app/site/src/assets/body.js' );
  module.hot.accept( '/srv/dev/posts-bundle.js' );
}
