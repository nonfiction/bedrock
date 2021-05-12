<?php
namespace nf;
use \Timber\Timber;
use function nf\import;


class BlockType {

  private static $block_types = [];

  public static $name = null;
  public static $args = [];
  public static $props = [];


  public static function register_block_type( $json = [], $override = [] ) {

    // If the first paramter is a path to .json file, import that 
    if ( (is_string($json)) and (ends_with($json, '.json')) ) {
      $json = import($json);
    }

    // Combine json with override
    $args = array_merge($json, $override);

    // Look for $name inside $args
    if (is_array($json)) {
      $name = ($args['name']) ?? false;
    }

    // If no name has been passed, bail out
    if ( empty($name) ) return false;

    // If this block type has already been registered, bail out
    if ( isset( self::$block_types[$name] ) ) return false;
    self::$block_types[$name] = $name;
    
    // Save $name and reset $args, $props
    static::$name = $name;
    static::$args = [];
    static::$props = [];

    // Post Meta
    static::$props['meta'] = $args['meta'] ?? [];
    unset($args['meta']);

    // Set default values for args array
    $args['render_callback'] ??= false;

    // If there's a render value, overwrite the render_callback
    if ( isset($args['render']) ) {
      $args['render_callback'] = static::render_callback($args['render']);
    }
    unset($args['render']);

    // Save $args to object
    static::$args = $args;
    // do_action( 'qm/debug', static::$args );

    // Register block
    static::register_custom_block_type();

    // Register meta
    static::register_post_meta();
  }


  protected static function render_callback( $render ) {
    return function( $attributes, $inner = 'default') use ($render) {
    
      // Merge the Timber context with the WP attributes, add inner blocks
      $context = array_merge( Timber::context(), $attributes );
      $context['inner'] = $inner;

      // Run the passed render() function to get the twig template and updated context
      $template = ($render)( $context );

      // do_action( 'qm/debug', 'after: ' . $context['inner'] );

      // Get the compiled template from twig file
      if ( ends_with($template, '.twig') ) {
        $html = Timber::render( $template, $context );

      // ...or the compiled template from a twig string
      } else {
        $html = Timber::compile_string( $template, $context );
      }

      // do_action( 'qm/debug', 'html: ' . $html );

      // Return the compiled template
      return $html;

    };
  }


  protected static function register_custom_block_type() {

    if ( !in_array( static::$name, self::$core_block_types ) ) {
      register_block_type( static::$name, array_merge([

      ], static::$args) ); 
    }
  }



  protected static function register_post_meta() {
    do_action( 'qm/debug', static::$props['meta'] );
    foreach(static::$props['meta'] as $meta_args) {

      $post_type = $meta_args['post_type'] ?? '';
      unset($meta_args['post_type']);

      $meta_key = $meta_args['key'] ?? false; 
      unset($meta_args['key']);

      if ( $meta_key ) {
        register_post_meta( $post_type, $meta_key, array_merge([

          'show_in_rest' => true,
          'type' => 'string', // string, boolean, integer, number, array, object
          'single' => true,

        // ], static::$meta_args) ); 
        ], $meta_args) ); 
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
