<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;

BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Extract values from context
    // foreach(['icon_url','icon_id'] as $key) { $context[$key] ??= ''; }
    // ['icon_url' => $img_url, 'icon_id' => $img_id] = $context;
    //
    // $context['heading'] ??= "YYC";
    // $context['content'] ??= "Head Office";
    // $context['icon_url'] = "https://unsplash.it/300/300";


    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-icon-text {{ className }}" style="{{ style }}"> 
        <figure class="nf-icon-text__figure"><img rel="{{ icon_id }}" src={{ icon_url }} /></figure>
        <div class="nf-icon-text__heading">{{ heading }}</div>
        <div class="nf-icon-text__content">{{ content }}</div>
      </div>

    END);
  },

]);
