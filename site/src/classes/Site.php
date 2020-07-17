<?php

namespace nf;
use \Timber;

class Site {

  private static $constructed = false;


  public function __construct() {
    if ( !self::$constructed ) {

      // Load theme directory inside this plugin
      register_theme_directory( dirname( __DIR__, 2 ) );

      // Initalize Timber and set the directory
      $timber = new Timber\Timber();

      // Set the default template directories
      Timber::$dirname = ['../src/views','../src/layouts'];

      // Also include all post type directories
      foreach (glob( dirname( __DIR__, 1 ) . '/posts/*' ) as $dir) {
        [ $base, $post ] = explode( '/posts/', $dir );
        Timber::$dirname[] = "../src/posts/$post";
        Timber::$dirname[] = "../src/posts/$post/views";
      }

      // Support HTML5 by default
      $this->support( 'html5', ['comment-form','comment-list','search-form','gallery','caption'] );

      // Add to settings menu: Reinitalize 
      add_action('admin_menu', function() {
        add_submenu_page( 'options-general.php', 'Reinitialize', 'Reinitalize', 'manage_options', 'reinitalize', [ $this, 'reinitialize' ], 100);
      }, 100);

      // Automatically run reinitalize at least once
      add_action('init', function() {
        if ( is_blog_installed() ) {
          if ( '1' !== get_option( 'nf_initialized') ) {
            self::initialize();
          }
        }
      });

      self::$constructed = true;
    }
  }


  public $assets = [];
  public function assets( $manifest_path ) {
    $this->assets = new Assets( $manifest_path );
  }


  public function load($path) {
    foreach (glob($path) as $file) {
      require_once $file;
    }
  }



  public static function initialize() {
    // Mark that this has been automatically done once 
    update_option( 'nf_initialized', '1' );

    // Flush post types and reset capablities
    PostType::deactivate_all();
    PostType::activate_all();

    // Configure Admin user color setting
    wp_update_user( [ 'ID' => 1, 'admin_color' => 'midnight' ] );
  }

  public function reinitialize() {
    self::initialize();
    echo "<h1>Reinitalize</h1>";
    echo "<p>...done!</p>";
  }


  // Add to theme support
  public function support( $feature, ...$args ) {
    add_action( 'after_setup_theme', function() use($feature, $args) {
      add_theme_support( $feature, ...$args );
    });
  }


  // Add to timber context
  public function context( $key, $value ) {
    add_filter( 'timber/context', function($context) use($key, $value) {
      global $post;

      // If the value is a function, evaluate it and set the value
      if ( is_callable($value) ) {
        $value = ($value)(new Timber\Post($post));

      // If the value has a menu name, build a menu and set the value
      } elseif ( (is_array($value)) and (isset($value['menu'])) )  {

        // Build menu and clean classnames
        $menu_name = $value['menu'] ?? '';
        $menu = new Timber\Menu( $menu_name, $value );
        $this->clean_menu_items( $menu->items );
        $value = $menu;
      }

      // Set the value to the context
      $context[$key] = $value;
      return $context;
    });
  }


  // Recursive function for tweaking menu classnames
  private function clean_menu_items( $items ) {
    foreach( $items as $item ) {

      $classes = [ 'menu-' . $item->slug ];
      if ( $item->current ) $classes[] = 'current';
      if ( $item->current_item_parent ) $classes[] = 'parent';
      if ( $item->current_item_ancestor ) $classes[] = 'ancestor';
      if ( $item->current or $item->current_item_parent or $item->current_item_ancestor ) {
        $classes[] = 'open';
      }
      $item->classes = $classes;
      $item->class = implode( ' ', $classes );

      if ( $item->children ) {
        $this->clean_menu_items( $item->children );
      }
    }
  }


  public function twig( $callback ) {
    add_filter( 'timber/twig', function( $twig ) use( $callback ) {
      if ( is_callable($callback) ) {
        return ($callback)($twig);
      }
    });
  }

  public function extension( $value ) {
    add_filter( 'timber/twig', function( $twig ) use( $value ) {
      $twig->addExtension($value);
      return $twig;
    });
  }

  public function filter( $value ) {
    add_filter( 'timber/twig', function( $twig ) use( $value ) {
      $twig->addFilter($value);
      return $twig;
    });
  }


}





/* Inflections available under nf namespace */

function pluralize(   ...$args ) { return \ICanBoogie\pluralize(   ...$args ); }
function singularize( ...$args ) { return \ICanBoogie\singularize( ...$args ); }
function underscore(  ...$args ) { return \ICanBoogie\underscore(  ...$args ); }
function hyphenate(   ...$args ) { return \ICanBoogie\hyphenate(   ...$args ); }
function humanize(    ...$args ) { return \ICanBoogie\humanize(    ...$args ); }
function titleize(    ...$args ) { return \ICanBoogie\titleize(    ...$args ); }


/* Utility Functions */

// Attempt to call an Intervention module
// https://github.com/soberwp/intervention
function add_intervention( $key, ...$args ) {
  add_action( 'init', function() use( $key, $args ) {
    if ( function_exists('\Sober\Intervention\intervention') ) {
      \Sober\Intervention\intervention( $key, ...$args );
    }
  });
}


function import( $path ) {
  if ( !is_string($path) ) return [];
  
  if ( !file_exists($path) ) return [];
  
  if ( strpos($path, '.json') !== false ) {
    return json_decode( file_get_contents($path), true );
  }

  return [];
}


function css($array) {
  return implode('; ', array_map(
    function($k, $v) { return $k . ': ' . $v; }, 
      array_keys($array), 
      array_values($array)
    )
  );
}


function m( $array_1, $array_2 ) {
  return array_merge( $array_1, $array_2 );
}

function dump($val) {
  echo "<br>";
  echo "<hr>";
  echo "<pre>";
  var_dump($val);
  echo "</pre>";
  echo "<hr>";
  echo "<br>";
}
