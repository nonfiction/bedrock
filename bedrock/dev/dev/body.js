// This is the <body> entry file for webpack--do not edit!

// WP hook: wp_enqueue_scripts (in footer)
import '/srv/web/app/site/assets/body.js';

if (module.hot) {
  module.hot.accept( '/srv/web/app/site/assets/body.js' );
}
