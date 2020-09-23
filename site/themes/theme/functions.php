<?php
namespace nf;
use \Timber\Timber;
use \Twig\TwigFunction;
use \Twig\TwigFilter;

add_theme_support( 'menus' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'align-wide' );
remove_theme_support( 'core-block-patterns' );


// Set values used in most templates
add_filter( 'timber/context', function($context) {

  $context['site'] = new \Timber\Site();
  $context['primary_menu'] = new Menu( 'Primary' );

  $context['img'] = home_url() . '/app/site/assets/img';
  $context['s'] = get_search_query();

  $context['post'] = PostType::get_post();
  $context['posts'] = PostType::get_posts();

  return $context;

});


// Add some additional twig functions
add_filter( 'timber/twig', function( $twig ) {

  // Adding a function.
  $twig->addFunction( new TwigFunction( 'edit_post_link', 'edit_post_link' ) );

  // Query posts from twig
  $twig->addFunction( new TwigFunction( 'PostQuery', function( $args ) {
     return PostType::get_posts( $args );
  }));

  // Adding functions as filters.
  $twig->addFilter( new TwigFilter( 'extract_image', function( $input ) {
    if ( preg_match("%(?<=src=\")([^\"])+(png|jpg|jpeg|gif|svg)%i",$input,$result)) {
      return $result[0];
    }
  }));

  return $twig;

});
