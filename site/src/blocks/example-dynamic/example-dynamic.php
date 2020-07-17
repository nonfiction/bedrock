<?php
namespace nf;

use \Timber\Timber;
use \Timber\Post;
use \Timber\PostQuery;


BlockType::register_block_type( 'nf/example-dynamic', [

  // These attributes must match index.js
  'attributes' => [
    'align' => [
      'type' => 'string',
    ],
    'content' => [
      'type' => 'string',
    ],
  ],


  // Render the twig template using $attributes
  'render_callback' => function( $attributes, $inner = '' ) { 
    $context = array_merge( Timber::context(), $attributes );
    $context['inner'] = $inner;
    return Timber::compile( 'example-dynamic.twig', $context );
  }

]);
