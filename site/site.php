<?php
/*
Plugin Name: Website Plugin & Theme
Description: Nonfiction's configuration for this website
Author: nonfiction studios
Version: 1.0
*/

namespace nf;

global $site;
$site = new \nf\Site();

// Load configuration tweaks
$site->load( __DIR__ . '/src/tweaks/*.php');

// Load block types
$site->load( __DIR__ . '/src/blocks/*/*.php');

// Load post types
$site->load( __DIR__ . '/src/posts/*/*.php');

// Load the manifest.json to register/enqueue webpack assets
$env = ( WP_ENV === 'development' ) ? '-dev' : '';
$site->assets( __DIR__ . "/dist/manifest${env}.json" );
