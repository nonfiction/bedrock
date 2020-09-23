<?php // Clean up the extra stuff WP adds to the <body>
namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;


// Add and remove body_class() classes
add_filter('body_class', function($classes){

  // Add post/page slug if not present
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Remove unnecessary classes
  $classes = array_diff($classes, [
    'post-template-default',
    'page-template-default',
    'page-id-' . get_option('page_on_front')
  ]);

  return $classes;

});
