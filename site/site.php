<?php
/*
Plugin Name: Website Plugin & Theme
Description: Nonfiction's configuration for this website
Author: nonfiction studios
Version: 1.0
*/

namespace nf;

Site::init();
Site::load( '/tweaks/*.php');
Site::load( '/blocks/*/index.php');
Site::load( '/posts/*/index.php');
Site::load( '/posts/*/blocks/*/index.php');
$env = ( WP_ENV === 'development' ) ? '-dev' : '';
Site::assets( "/dist/manifest${env}.json" );