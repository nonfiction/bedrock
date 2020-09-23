<?php
namespace nf;
use \Timber;
use \Timber\Image;

class TeamMember extends PostType {

  protected static function before_register_post_type() {

    static::$args['admin_cols']['photo']['function'] = function() {
      global $post;
      $img = new Image(get_post_meta($post->ID, 'photo_small_id', true));
      echo "<img width='50' src='" . $img->src('thumbnail') . "' />";
    };

  }

}


// Register Custom Post Type
add_action( 'init', function() {

  TeamMember::register_post_type( __DIR__ . '/post.json' );

}, 18);
