<?php
use nf\BlockType;
use function nf\import;
use function nf\css;


BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    $context['term'] ??= '__none';

    return (<<<END

      <div class="nf-tabbed-posts">
        <div class="nf-tabbed-posts__grid">
          <div class="nf-tabbed-posts__heading">
            <h2 class="is-style-decorative">{{ heading }}</h2>
            <p>{{ content }}</p>
          </div>
          <ul class="nf-tabbed-posts__tabs">
            {% for post in PostQuery({ post_type: 'post', 'category_name': term, posts_per_page: 5, }) %}
              <li{% if loop.first %} class="is-active"{% endif %}>
                <a rel="{{ post.slug }}" href="#{{ post.slug }}">{{ post.title}}</a>
              </li>
            {% endfor %}
          </ul>
          <ul class="nf-tabbed-posts__posts">
            {% for post in PostQuery({ post_type: 'post', 'category_name': term, posts_per_page: 5, }) %}
              <li class="nf-tabbed-post nf-tabbed-post-{{ post.slug }}{% if loop.first %} is-active{% endif %}" style="background-image:url({{ post.thumbnail.src }});">
                <div class="nf-tabbed-post__panel">
                  <h3 class="nf-tabbed-post__heading">{{ post.title }}</h3>
                  <div class="nf-tabbed-post__content">{{ post.content }}</div>
                </div>
              </li>
            {% endfor %}
          </ul>
        </div>
      </div>

    END);

  },

]);
