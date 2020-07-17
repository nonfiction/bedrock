<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;

BlockType::register_block_type( 'nf/staff-member', array_merge([

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Optionally modify the context
    $context['foo'] = 'bar';

    // Extract values from context
    // ['background_url' => $img_url, 'background_id' => $img_id] = $context;
    $img_url = $context['photo_url'] ?? '';

    // $context['photo_url'] = "https://unsplash.it/235/270";
    // $context['name'] = "Bob Manly";
    // $context['title'] = "Chief Squatter";
    // $context['linkedin'] = "www.ca.linkedin.com/in/shaebird";
    // $context['email'] = "shae@indigenoustourismalberta.ca";
    // $context['content'] = "asdlfjasdlkf jsadlkfjsadlkfj salkf j  asfklj jasdf";

    // Add new values to context
    // $context['style'] = css([ 'background-image' => "url($img_url)" ]);

    // Return a twig template for Timber to render
    return (<<<END

      <div class="staff-member" style="{{ style }}"> 

        <figure class="staff-member__figure">
          {% if photo_url %}
          <img class="staff-member__photo" src="{{ photo_url }}" />
          {% endif %}

          <h3 class="staff-member__name">{{ name }}</h3>
          <h4 class="staff-member__title">{{ title }}</h3>

          {% if linkedin %}
          <a class="staff-member__link" href="https://{{ linkedin }}">
            <img src="{{ img }}/icon/staff-linkedin.svg" />
            <span>{{ linkedin }}</span>
          </a>
          {% endif %}

          {% if email %}
          <a class="staff-member__link" href="mailto:{{ email }}">
            <img src="{{ img }}/icon/staff-email.svg" />
            <span>{{ email }}</span>
          </a>
          {% endif %}
        </figure>

        <div class="staff-member__content">{{ content }}</div>
      </div>

    END);
  },

], import( __DIR__ . '/block.json' )) );
