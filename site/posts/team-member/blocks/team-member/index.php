<?php
use nf\BlockType;
use function nf\import;
use function nf\css;
use Timber\Timber;

// BlockType::register_block_type( 'nf/team-member', array_merge([
BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    $context['id'] ??= 1;

    // Retrieve post
    $post = Timber::get_post([
      'p' => $context['id'],
      'post_type' => 'team_member',
    ]); 

    // Extract values from context
    // $context['heading'] = $post->title;
    $context['post'] = $post;


    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-team-member {{ className }}" style="{{ style }}"> 

        <a class="nf-team-member__link" href="#" style="background-image: url( {{ post.meta('photo_small') }} );">
          <div class="nf-team-member__name-role">
            <div class="nf-team-member__name">{{ post.title }}</div>
            <div class="nf-team-member__role">{{ post.meta('role') }}</div>
          </div>
        </a>

        <div class="nf-team-member__overlay">
          <div class="nf-team-member__details">
            <figure class="nf-team-member__figure">
              <img class="nf-team-member__image" src="{{ post.meta('photo_large') }}" />
            </figure>
            <div class="nf-team-member__text">
              <div class="nf-team-member__name">{{ post.title }}</div>
              <div class="nf-team-member__role">{{ post.meta('role') }}</div>
              <div class="nf-team-member__content">{{ post.content }}</div>
            </div>
          </div>
        </div>

      </div>

    END);
  },

]);
