<?php
namespace nf;
use \Timber;

class NewsRelease extends PostType { 

  protected static function after_register_post_type() {

    add_filter( 'posts_results', function( $posts ) {
      return array_map( function($post) {

        if ( 'news_release' == $post->post_type ) {
          if ( preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $post->post_content, $matches) ) {
            $post->first_img = $matches[1][0];
            return $post;
          }
        }

        return $post;
      }, $posts );
    });

  }

}


// Register Custom Post Type
add_action( 'init', function() {

  NewsRelease::register_post_type( __DIR__ . '/post.json' );

});
