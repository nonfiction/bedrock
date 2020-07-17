<?php
namespace nf;

add_intervention( 'remove-menu-items', [ 
  // 'dashboard', 
  // 'themes',
  'media', 
  'comments', 
  'admin.php?page=members-payments',
], 'all' );


add_intervention( 'add-menu-page', [
  'page_title'    => 'Media',
  'menu_title'    => 'Media',
  'menu_slug'     => 'upload.php',
  'function'      => '',
  'icon_url'      => 'admin-media',
  'position'      => 60
], 'all' );


add_intervention( 'add-menu-page', [
  'page_title'    => 'Menus',
  'menu_title'    => 'Menus',
  'menu_slug'     => 'nav-menus.php',
  'function'      => '',
  'icon_url'      => 'menu',
  'position'      => 62
], 'all' );


add_action( 'admin_bar_menu', function(){
  global $wp_admin_bar;   
  $wp_admin_bar->remove_node( 'masterpress' );
}, 1000);

