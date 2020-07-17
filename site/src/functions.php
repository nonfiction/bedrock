<?php
namespace nf;
use Timber\Timber;

global $site;

$site->support( 'menus' );
$site->support( 'automatic-feed-links' );
$site->support( 'title-tag' );
$site->support( 'post-thumbnails' );
$site->support( 'align-wide' );

$site->context( 'site', new \Timber\Site() );
$site->context( 'primary_menu', [ 'menu' => 'primary', 'depth' => 2 ] );

// Path to assets image directory
$site->context('img', home_url() . '/app/site/src/assets/img');
