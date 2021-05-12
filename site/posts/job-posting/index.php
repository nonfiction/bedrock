<?php
namespace nf;
use \Timber;

class JobPosting extends PostType {

  protected static function after_register_post_type() {
    add_shortcode( 'jobs', function( $attr ) {
    
      $posts = self::get_posts([
        'post_type' => 'job_posting',
        'orderby' => 'date',
        'order' => 'DESC',
      ]);

      return Timber::compile_string( <<<END

        <ul class="nf-job-postings">
          {% for post in posts %}
            <li class="nf-job-posting">
              <a href="{{ post.link }}">{{ post.title }}</a>
            </li>
          {% endfor %}
        </ul>

      END, ['posts' => $posts] );
    
    });
  }

}


// Register Custom Post Type
add_action( 'init', function() {

  JobPosting::register_post_type([
    "menu_icon" => "dashicons-pressthis",
  ]);

}, 18);
