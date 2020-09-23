<?php
namespace nf;
use Timber;

class Testimonial extends PostType {

  protected static function after_register_post_type() {
    add_shortcode( 'testimonial_slider', function( $attr = [] ) {

      $section = $attr['section'] ?? '__no-section__';

      $posts = self::get_posts([
        'post_type' => 'testimonial',
        'section' => $section,
      ]);

      return Timber::compile_string( <<<END

        <div class="nf-testimonials">
          {% for post in posts %}
            <div class="nf-testimonial">
              <blockquote class="nf-testimonial__quote">{{ post.content }}</blockquote>
              <cite class="nf-testimonial__cite">
                <div class="nf-testimonial__author">{{ post.title }}</div>
                <div class="nf-testimonial__role">{{ post.meta('role') }}</div>
              </cite>
            </div>
          {% endfor %}
        </div>

      END, ['posts' => $posts] );
    
    });
  }

}


// Register Custom Post Type
add_action( 'init', function() {

  Testimonial::register_post_type( __DIR__ . '/post.json' );

}, 18);
