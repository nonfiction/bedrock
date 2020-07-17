<?php
namespace nf;
use \Timber\Timber;


class BlockType {

  public static $blocks = [];

  public static function register_block_type( $name, $args = [] ) {
    self::$blocks[$name] = new BlockType( $name, $args );
  }

  public $name = null;
  public $wp_block_type = null;

  public function __construct( $name, $args = [] ) {
    $this->name = $name;
    $this->meta = $args['meta'] ?? [];
    unset($args['meta']);

    if ( !in_array( $this->name, self::$core_block_types ) ) {

      // Register block
      $this->register_block( $args );

      // Register meta
      $this->register_meta();
    }
  }

  private function register_block($args = []) {

    // Set default values for args array
    $args['render_callback'] ??= false;
    $render = $args['render'] ?? false;
    unset($args['render']);

    // If there's a render value, overwrite the render_callback
    if ( $render ) {
      $args['render_callback'] = function( $attributes, $inner = '' ) use ($render) { 

        // Merge the Timber context with the WP attributes, add inner blocks
        $context = array_merge( Timber::context(), $attributes );
        $context['inner'] = $inner;

        // Run the passed render() function to get the twig template and updated context
        $template = ($render)( $context );

        // Return the compiled template
        return Timber::compile_string( $template, $context );
      };
    }

    add_action( 'init', function() use($args) {
      $this->wp_block_type = register_block_type( $this->name, $args );
    });
  }


  private function register_meta() {
    foreach($this->meta as $args) {

      $post_type = $args['post_type'] ?? '';
      unset($args['post_type']);

      $meta_key = $args['key'] ?? false; 
      unset($args['key']);

      $args['show_in_rest'] ??= true;
      $args['single'] ??= true;
      $args['type'] ??= 'string';

      if ( $meta_key ) {
        add_action( 'init', function() use($post_type, $meta_key, $args) {
          register_post_meta( $post_type, $meta_key, $args );
        });
      }
    }
  }

  public static $core_block_types = [

    /* [COMMON] */
    'core/image',
    'core/paragraph',
    'core/heading',
    'core/gallery',
    'core/list',
    'core/quote',
    'core/audio',
    'core/cover',
    'core/file',
    'core/video',

    /* [FORMATTING] */
    'core/code',
    'core/classic',
    'core/html',
    'core/preformatted',
    'core/pullquote',
    'core/table',
    'core/verse',

    /* [LAYOUT] */
    'core/page-break',
    'core/buttons',
    'core/columns',
    'core/group',
    'core/media-text',
    'core/more',
    'core/separator',
    'core/spacer',

    /* [WIDGETS] */
    'core/archives',
    'core/shortcode',
    'core/calendar',
    'core/categories',
    'core/latest-posts',
    'core/rss',
    'core/search',
    'core/social-icons',
    'core/tag-cloud',

    /* [EMBEDS] */
    'core/embed',
    'core-embed/twitter',
    'core-embed/youtube',
    'core-embed/facebook',
    'core-embed/instagram',
    'core-embed/wordpress',
    'core-embed/soundcloud',
    'core-embed/spotify',
    'core-embed/flickr',
    'core-embed/vimeo',
    'core-embed/animoto',
    'core-embed/cloudup',
    'core-embed/crowdsignal',
    'core-embed/dailymotion',
    'core-embed/hulu',
    'core-embed/imgur',
    'core-embed/issuu',
    'core-embed/kickstarter',
    'core-embed/meetup-com',
    'core-embed/mixcloud',
    'core-embed/reddit',
    'core-embed/reverbnation',
    'core-embed/screencast',
    'core-embed/scribd',
    'core-embed/slideshare',
    'core-embed/smugmug',
    'core-embed/speaker-deck',
    'core-embed/tiktok',
    'core-embed/ted',
    'core-embed/tumblr',
    'core-embed/videopress',
    'core-embed/wordpress-tv',
    'core-embed/amazon-kindle',
  ];

}
