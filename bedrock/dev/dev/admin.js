// This is the admin entry file for webpack--do not edit!

// WP hook: admin_enqueue_scripts (in footer)
import '/srv/web/app/site/assets/admin.js';

if (module.hot) {
  module.hot.accept( '/srv/web/app/site/assets/admin.js' );
}
