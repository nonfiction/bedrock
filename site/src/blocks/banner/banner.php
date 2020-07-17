<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;

BlockType::register_block_type( 'nf/banner', array_merge([

  // 'meta' => [
  //   [ 'key' => 'foo', 'type' => 'string' ],
  //   [ 'key' => 'bar', 'type' => 'string' ]
  // ]

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Optionally modify the context
    $context['foo'] = 'bar';

    // Extract values from context
    // ['background_url' => $img_url, 'background_id' => $img_id] = $context;
    $img_url = $context['background_url'] ?? '';


    // Add new values to context
    $context['style'] = css([ 'background-image' => "url($img_url)" ]);

    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-banner" style="{{ style }}"> 
        <div class="nf-banner__inner">
          <h1 class="nf-banner__heading">{{ heading }}</h1>
          <p class="nf-banner__content">{{ content }}</p>
        </div>
      </div>

    END);
  },

], import( __DIR__ . '/block.json' )) );
