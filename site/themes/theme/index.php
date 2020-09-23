<?php
namespace nf;
use \Timber\Timber;

// ------------------------------------------------------------
// Get Context
// ------------------------------------------------------------
$context = Timber::context();

$post = $context['post'];
$posts = $context['posts'];
$slug = $type = '';

if ($post) {
  $slug = $post->slug;
  $type = $post->type;
}

// ------------------------------------------------------------
// Populate template array from least specific to most specific
// ------------------------------------------------------------
$templates = ['index.twig'];

if ( is_page() ) {
  array_unshift( $templates, 'page.twig' );

  array_unshift( $templates, hyphenate("page-{$slug}.twig") );
  array_unshift( $templates, hyphenate("page/single.twig") );
  array_unshift( $templates, hyphenate("page/page.twig") );
  array_unshift( $templates, hyphenate("page/single-{$slug}.twig") );
  array_unshift( $templates, hyphenate("page/page-{$slug}.twig") );
  array_unshift( $templates, hyphenate("page/views/single.twig") );
  array_unshift( $templates, hyphenate("page/views/page.twig") );
  array_unshift( $templates, hyphenate("page/views/single-{$slug}.twig") );
  array_unshift( $templates, hyphenate("page/views/page-{$slug}.twig") );
}

if ( is_home() ) {
  array_unshift( $templates, 'home.twig' );
}

if ( is_front_page() ) {
  array_unshift( $templates, 'front.twig' );
}

if ( is_single() ) {

  array_unshift( $templates, 'single.twig' );
  array_unshift( $templates, hyphenate("single-{$type}.twig") );
  array_unshift( $templates, hyphenate("single-{$type}-{$slug}.twig") );
  array_unshift( $templates, hyphenate("{$type}/single.twig") );
  array_unshift( $templates, hyphenate("{$type}/single-{$slug}.twig") );
  array_unshift( $templates, hyphenate("{$type}/views/single.twig") );
  array_unshift( $templates, hyphenate("{$type}/views/single-{$slug}.twig") );

  if ( post_password_required( $post->ID ) ) {
    array_unshift( $templates, 'single-password.twig' );
    array_unshift( $templates, hyphenate("single-password-{$type}.twig") );
    array_unshift( $templates, hyphenate("single-password-{$type}-{$slug}.twig") );
    array_unshift( $templates, hyphenate("{$type}/single-password.twig") );
    array_unshift( $templates, hyphenate("{$type}/single-password-{$slug}.twig") );
    array_unshift( $templates, hyphenate("{$type}/views/single-password.twig") );
    array_unshift( $templates, hyphenate("{$type}/views/single-password-{$slug}.twig") );
  }
}

if ( is_author() ) {
  array_unshift( $templates, 'author.twig' );

  if ( isset( $wp_query->query_vars['author'] ) ) {
    $author_slug = $wp_query->query_vars['author'];
    $author = new \Timber\User( $author_slug );
    $context['author'] ??= $author;
    $context['title']  ??= 'Author Archives: ' . $author->name();
    array_unshift( $templates, "author-{$author_slug}.twig" );
  }
}

if ( is_archive() ) {

  if ( function_exists('\get_archive_thumbnail_src') ) {
    $context['archive_title'] = \get_the_archive_title();
    $context['archive_image'] = \get_archive_thumbnail_src();
    $context['archive_top'] = \get_archive_top_content();
    $context['archive_bottom'] = get_archive_bottom_content();
  }

  array_unshift( $templates, 'archive.twig' );
}

if ( is_tax() ) {
  array_unshift( $templates, 'taxonomy.twig' );
  $tax_slug = get_query_var('taxonomy');
  array_unshift( $templates, hyphenate("taxonomy-{$tax_slug}.twig") );
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

  } elseif ( is_year() ) {
    $context['title'] = 'Archive: ' . get_the_date( 'Y' );

  } 
  if ( is_tag() ) {
    $context['title'] = single_tag_title( '', false );

  } 
  if ( is_category() ) {
    $context['title'] = single_cat_title( '', false );
    $cat_slug = get_query_var('cat');
    array_unshift( $templates, hyphenate("archive-{$cat_slug}.twig") );
  } 
  if ( is_post_type_archive() ) {
    $context['title'] = post_type_archive_title( '', false );
    array_unshift( $templates, hyphenate("archive-{$type}.twig") );
    array_unshift( $templates, hyphenate("{$type}/archive.twig") );
    array_unshift( $templates, hyphenate("{$type}/views/archive.twig") );
  } 
  if ( is_tax() ) {
    $term = new \Timber\Term();
    $context['term'] = $term;
    $context['title'] ??= $term->taxonomy . ' - ' . $term->name;
  }
}

Timber::render( $templates, $context );
