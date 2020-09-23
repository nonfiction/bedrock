<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    $context['name'] ??= "";
    $context['role'] ??= "";
    $context['content'] ??= "";
    $context['photo_url'] ??= "https://unsplash.it/368/368";
    $context['width'] ??= "wide";
    $context['valign'] ??= "middle";
    $context['halign'] ??= "left";


    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-influencer is-{{ width }} is-{{ valign }} is-{{ halign }} {{ className }}" style="{{ style }}"> 
        <figure class="nf-influencer__figure">
          <img class="nf-influencer__photo" src="{{ photo_url }}" />
        </figure>
        <div class="nf-influencer__details">
          <div class="nf-influencer__text">
            <h3 class="nf-influencer__name">{{ name }}</h3>
            <h4 class="nf-influencer__role">{{ role }}</h4>
            <div class="nf-influencer__content">{{ content }}</div>
          </div>
        </div>
      </div>

    END);
  },

]);
