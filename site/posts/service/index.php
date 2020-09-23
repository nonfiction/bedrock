<?php
namespace nf;
use Timber;

class Service extends PostType {

  protected static function before_register_post_type() {
    // log(static::$props);
  }

  public function reading_time() {
    $words_per_minute = 228;

    $words   = str_word_count( wp_strip_all_tags( $this->content() ) );
    $minutes = round( $words / $words_per_minute );

    /* translators: %s: Time duration in minute or minutes. */
    return sprintf( _n( '%s minute', '%s minutes', $minutes ), (int) $minutes );
  }

}



// Register Custom Post Type
add_action( 'init', function() {

  Service::register_post_type( __DIR__ . '/post.json' );

} );
