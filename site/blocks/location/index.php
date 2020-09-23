<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Optionally modify the context
    $context['foo'] = 'bar';

    // Extract values from context
    // ['background_url' => $img_url, 'background_id' => $img_id] = $context;
    $img_url = $context['photo_url'] ?? '';

    $context['name'] ??= "YYC";
    $context['office'] ??= "Head Office";
    $context['address'] ??= "";
    $context['phone'] ??= "403.538.5641";
    $context['email'] ??= "info@info.com";


    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-location {{ className }}" style="{{ style }}"> 

        <div class="nf-location__name">{{ name }}</div>
        <div class="nf-location__office">{{ office }}</div>
        <div class="nf-location__address">{{ address }}</div>
        <div class="nf-location__phone">Telephone: <a href="tel:{{ phone }}">{{ phone }}</a></div>
        <div class="nf-location__email">Email: {{ email }}</div>

      </div>

    END);
  },

]);
