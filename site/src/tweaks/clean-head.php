<?php 
namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;


// Clean up the extra stuff WP adds to the <head>
add_action('init', function() {

  remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
  remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)
  remove_action('wp_head', 'index_rel_link'); // remove link to index page
  remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
  remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
  remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action('wp_head', 'wp_oembed_add_discovery_links');
  remove_action('wp_head', 'wp_oembed_add_host_js');
  remove_action('wp_head', 'rest_output_link_wp_head', 10); // remove api link

  remove_action('wp_head', 'wp_generator'); // remove wordpress version
  add_filter('the_generator', '__return_false'); // remove wordpress version from rss feeds
  add_filter('use_default_gallery_style', '__return_false');
  add_filter('show_recent_comments_widget_style', '__return_false');

});
