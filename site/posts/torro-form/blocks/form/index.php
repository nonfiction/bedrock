<?php
use nf\BlockType;
use function nf\import;
use function nf\css;
use Timber\Timber;

BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    $context['id'] ??= false;

    // Retrieve post
    $post = Timber::get_post([
      'p' => $context['id'],
      'post_type' => 'torro_form',
    ]); 

    // Extract values from context
    // $context['post'] = $post;

    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-form {{ className }}" style="{{ style }}"> 
        <h3 class="nf-form__heading">{{ heading|nl2br }}</h3>
        <div class="nf-form__content">{{ torro_form(id) }}</div>
      </div>

    END);
  },

]);
