<?php
namespace nf;
use Timber\Timber;

global $site;

$site->context('s', get_search_query());

$site->context( 'post', function($post) {
  $format_function = 'nf\format_' . get_post_type(); 
  if ( function_exists($format_function)) {
    $post = ($format_function)($post);
  }
  return $post;
});


$site->context( 'posts', function() {
  $posts = new \Timber\PostQuery();
  $posts = (is_array($posts)) ? $posts : Timber::get_posts();
  $format_function = 'nf\format_' . get_post_type(); 
  if ($posts) {
    if ( function_exists($format_function)) {
      $posts = array_map( $format_function, $posts );
    }
  }
  return $posts;
});



// ------------------------------------------------------------
// Set Context
// ------------------------------------------------------------
$context = Timber::context();

$post = $context['post'];
$posts = $context['posts'];


// ------------------------------------------------------------
// Populate template array from least specific to most specific
// ------------------------------------------------------------
$templates = ['index.twig'];

if ( is_page() ) {
  array_unshift( $templates, 'page.twig' );
  array_unshift( $templates, 'page-' . $post->post_name . '.twig' );
}

if ( is_home() ) {
  array_unshift( $templates, 'home.twig' );
}

if ( is_front_page() ) {
  array_unshift( $templates, 'front.twig' );
}

if ( is_single() ) {

  array_unshift( $templates, 'single.twig' );
  array_unshift( $templates, hyphenate('single-' . $post->post_type . '.twig') );
  array_unshift( $templates, 'single-' . $post->post_name . '.twig' );

  if ( post_password_required( $post->ID ) ) {
    array_unshift( $templates, 'single-password.twig' );
  }
}

if ( is_author() ) {
  array_unshift( $templates, 'author.twig' );

  if ( isset( $wp_query->query_vars['author'] ) ) {
    $author = new User( $wp_query->query_vars['author'] );
    $context['author'] ??= $author;
    $context['title']  ??= 'Author Archives: ' . $author->name();
    array_unshift( $templates, 'author-' . $wp_query->query_vars['author'] . '.twig' );
  }
}

if ( is_archive() ) {
  array_unshift( $templates, 'archive.twig' );
}

if ( is_tax() ) {
  array_unshift( $templates, 'taxonomy.twig' );
  array_unshift( $templates, 'taxonomy-' . hyphenate(get_query_var('taxonomy')) . '.twig' );
}

if ( is_404() ) {
  array_unshift( $templates, '404.twig' );
}

if ( is_search() ) {
  $context['title'] ??= 'Search results for ' . get_search_query();
  array_unshift( $templates, 'search.twig' );
}



if ( is_archive() ) {
  $context['title'] ??= 'Archive';

  if ( is_day() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'F D, Y' );

  } elseif ( is_month() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'F Y' );
    // var_dump($templates);
    // var_dump($context['title']);

  } elseif ( is_year() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'Y' );

  } 
  if ( is_tag() ) {
    $context['title'] = single_tag_title( '', false );

  } 
  if ( is_category() ) {
    $context['title'] = single_cat_title( '', false );
    array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );

  } 
  if ( is_post_type_archive() ) {
    $context['title'] = post_type_archive_title( '', false );
    // array_unshift( $templates, "archive-${post_type}.twig" );
    array_unshift( $templates, hyphenate('archive-' . $post->post_type . '.twig') );
    // var_dump($templates);

  } 
  if ( is_tax() ) {
    $term = new \Timber\Term();
    $context['term'] = $term;
    $context['title'] ??= $term->taxonomy . ' - ' . $term->name;
  }
}

Timber::render( $templates, $context );
