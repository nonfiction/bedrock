<?php
namespace nf;

// Hide builtin post type
add_action( 'admin_menu', function(){
  remove_menu_page( 'edit.php' );
});

add_action( 'admin_bar_menu', function(){
  global $wp_admin_bar;   
  $wp_admin_bar->remove_node( 'new-post' );
}, 80);
