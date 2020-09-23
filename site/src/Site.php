<?php

namespace nf;
use \Timber;

class Site {

  private static $path = __DIR__;
  private static $init = false;

  public static function init( $path = false ) {

    // Only run this once
    if ( static::$init ) return;
    static::$init = true;

    // Set path, default to parent directory
    static::$path = ($path) ? $path : dirname( __DIR__, 1 );

    // Load theme directory inside this plugin
    register_theme_directory( static::$path . '/themes' );

    // Initalize Timber and set the directory
    $timber = new Timber\Timber();

    // Set the default template directories
    Timber::$dirname = [ '../../posts', '../../views' ];

    // Support HTML5 by default
    add_action( 'after_setup_theme', function() {
      add_theme_support( 'html5', ['comment-form','comment-list','search-form','gallery','caption'] );
    });

    // Add to settings menu: Reinitalize 
    add_action('admin_menu', function() {
      add_submenu_page( 'options-general.php', 'Reactivate', 'Reactivate', 'manage_options', 'reactivate', [ '\nf\Site', 'reactivate' ], 100);
    }, 100);

    // Automatically run reinitalize at least once
    add_action('init', function() {
      if ( is_blog_installed() ) {
        if ( '1' !== get_option( 'nf_activated') ) {
          static::activate();
        }
      }
    });

  }


  public static function assets( $manifest_path ) {
    $path = str_replace( '//', '/', static::$path .'/'. $manifest_path );
    // static::$assets = new Assets( $manifest_path );
    new Assets( $path );
  }


  public static function load($resource_path) {
    $path = str_replace( '//', '/', static::$path .'/'. $resource_path );
    foreach (glob($path) as $file) {
      require_once $file;
    }
  }


  public static function activate( $force = false ) {

    // Mark that this has been automatically done once 
    update_option( 'nf_activated', '1' );

    // Flush post types and reset capablities
    PostType::activate_all();

    // Configure Admin user color setting
    wp_update_user( [ 'ID' => 1, 'admin_color' => 'midnight' ] );
  }

  public static function reactivate() {
    static::activate();
    echo "<h1>Reactivate</h1>";
    echo "<p>...done!</p>";
  }

}


/* Utility Functions */

// Inflections available under nf namespace
function pluralize(   ...$args ) { return \ICanBoogie\pluralize(   ...$args ); }
function singularize( ...$args ) { return \ICanBoogie\singularize( ...$args ); }
function underscore(  ...$args ) { return \ICanBoogie\underscore(  ...$args ); }
function hyphenate(   ...$args ) { return \ICanBoogie\hyphenate(   ...$args ); }
function camelize(    ...$args ) { return \ICanBoogie\camelize(    ...$args ); }
function humanize(    ...$args ) { return \ICanBoogie\humanize(    ...$args ); }
function titleize(    ...$args ) { return \ICanBoogie\titleize(    ...$args ); }

// Custom pluralize inflection to ensure uniqueness
function unique_pluralize($word, $word_to_compare = false) {

  // If no word to compare is provided, use the word to pluralized
  $word_to_compare = ($word_to_compare) ? $word_to_compare : $word;

  // Pluralize the word
  $word = pluralize($word);

  // If the word matches the word to compare, add an s or es
  if ( $word == $word_to_compare ) {
    $word .= ( 's' == substr($word, -1) ) ? 'es' : 's';
  }

  return $word;
}


// Attempt to call an Intervention module
// https://github.com/soberwp/intervention
function add_intervention( $key, ...$args ) {
  add_action( 'init', function() use( $key, $args ) {
    if ( function_exists('\Sober\Intervention\intervention') ) {
      \Sober\Intervention\intervention( $key, ...$args );
    }
  });
}


// Read a json file and return an associative array
function import( $path ) {
  if ( is_array($path) ) return $path; 

  if ( !is_string($path) ) return [];
  
  if ( !file_exists($path) ) return [];
  
  if ( strpos($path, '.json') !== false ) {
    return json_decode( file_get_contents($path), true );
  }

  return [];
}

// Merge two json files or arrays together
function merge( $array_or_file_1 = [], $array_or_file_2 = [] ) {
  return array_merge( import($array_or_file_1), import($array_or_file_2) );
}

// Convert an associative array into a CSS string for a style="" attribute
function css($array) {
  return implode('; ', array_map(
    function($k, $v) { return $k . ': ' . $v; }, 
      array_keys($array), 
      array_values($array)
    )
  );
}


// return true if string haystack starts with needle
function starts_with( $haystack, $needle ) {
  $length = strlen( $needle );
  return substr( $haystack, 0, $length ) === $needle;
}

// return true if string haystack ends with needle
function ends_with( $haystack, $needle ) {
  $length = strlen( $needle );
  if( !$length ) {
    return true;
  }
  return substr( $haystack, -$length ) === $needle;
}


// Display the value in QueryMonitor or to the screen
function log($value, $var_dump = false) {
  do_action( 'qm/debug', $value );
  if ($var_dump) {
    echo "<br>";
    echo "<hr>";
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
    echo "<hr>";
    echo "<br>";
  }
}
