<?php 
namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;


// Remove feeds from the <head>
add_action('init', function() {

  remove_action('wp_head', 'feed_links', 2); // remove rss feed links
  remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

  // remove comments feed
  add_action('wp_head', 'ob_start', 1, 0);
  add_action('wp_head', function () {
    $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
    echo preg_replace($pattern, '', ob_get_clean());
  }, 3, 0);

});
