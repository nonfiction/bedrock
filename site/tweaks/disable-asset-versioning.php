<?php 
namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;


// Remove the query string from scripts and styles
foreach( ['script_loader_src','style_loader_src'] as $tag ) {
  add_filter( $tag, function($src) {
    return $src ? esc_url( remove_query_arg( 'ver', $src ) ) : false;
  }, 15, 1 );
}
