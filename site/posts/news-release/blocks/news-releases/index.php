<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

// BlockType::register_block_type( 'nf/news-releases', array_merge([
BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Retrieve post
    $context['posts'] = Timber::get_posts([ 
      'post_type' => 'news_release', 
      // 'orderby' => 'publish', 
      // 'order' => 'ASC', 
      'posts_per_page' => 3,
    ]);

    // Return a twig template for Timber to render
    return (<<<END

      <div class="nf-news-release-list__wrapper">

        <h2 class="nf-news-release-list-heading is-style-decorative">What&rsquo;s New</h2>
        <div class="nf-news-release-list wp-block-nf-grid" data-columns="3">
          {% for post in posts %}
            {% include "news-release/views/tease.twig" %}
          {% endfor %}
        </div>

        <div class="wp-block-buttons">
          <div class="wp-block-button is-float-right">
            <a class="wp-block-button__link" href="/news">See All Articles</a>
          </div>
        </div>

      </div>


    END);
  },

]);
