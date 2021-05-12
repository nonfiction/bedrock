<?php
namespace nf;

class PostType extends \Timber\Post {

  private static $post_types = [];
  public static $classmap = [];

  // Wrapper for Timber::get_post to always use classmap
  public static function get_post( $query=false, $classmap=false ) {
    $classmap = ($classmap) ? $classmap : self::$classmap;
    return \Timber::get_post( $query, $classmap );
  }

  // Wrapper for Timber::get_posts to always use classmap
  public static function get_posts( $query=false, $classmap=false, $return=false ) {
    $classmap = ($classmap) ? $classmap : self::$classmap;
    return \Timber::get_posts( $query, $classmap, $return );
  }

  public static $name = null;
  public static $names = [];
  public static $args = [];
  public static $props = [];


  public static function register_post_type( $json = [], $override = [] ) {

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
    // if ( empty($name) ) return false;

    // If no name passed, determine one from the classname
    if ( empty($name) ) {
      $name = underscore(preg_replace('/^.*\\\s*/', '', get_called_class()));
    }

    // Generate names from $name
    $names = static::generate_names( $name, $args['names'] ?? [] );
    $name = $names['key_single']; // update $name arg in case it got modified 
    unset($args['names']);

    // If this post type has already been registered, bail out
    if ( isset( self::$post_types[$name] ) ) return false;
    self::$post_types[$name] = $name;
   
    // Save $names, $name and reset $args, $props
    static::$names = $names;
    static::$name = $name;
    static::$args = [];
    static::$props = [];

    // Add to classmap (Timber classes)
    self::$classmap = array_merge(self::$classmap, [ $name => $names['class'] ]);
    add_filter( 'timber/post/classmap', function( $classmap ) use( $name, $names) {
      return array_merge( $classmap, [ $name => $names['class'] ] );
    } );

    // Post Meta
    static::$props['meta'] = $args['meta'] ?? [];
    unset($args['meta']);

    // Blocks
    static::$props['blocks'] = $args['blocks'] ?? true;
    unset($args['blocks']);

    // Custom Metaboxes CMB2
    // https://github.com/CMB2/CMB2/wiki/Field-Types
    static::$props['metaboxes'] = $args['metaboxes'] ?? [];
    unset($args['metaboxes']);

    // https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
    static::$props['taxonomies'] = $args['taxonomies'] ?? [];
    unset($args['taxonomies']);


    // No archive pages by default
    static::$props['has_archive'] = false;

    // If has_archive is set, use the plural slug
    if ( ( isset($args['has_archive']) ) && ( $args['has_archive'] !== false ) ) {
      static::$props['has_archive'] = $names['slug_plural'];
    } 
    unset($args['has_archive']);


    // Save args to object
    static::$args = $args;

    static::before_register_post_type();

    // Finally register or customize the post type
    if ( static::is_native_post_type() ) {
      static::register_native_post_type();
    } else {
      static::register_custom_post_type();
    }

    static::register_taxonomies();
    static::register_post_meta();
    static::register_allowed_block_types();
    static::register_block_categories();
    static::register_custom_meta_boxes();

    static::after_register_post_type();

    static::activate();

  }



  private static function before_register_post_type() {
    return true;
  }

  private static function after_register_post_type() {
    return true;
  }

  // Returns names array
  private static function generate_names( $name, $names = [] ) {

    // Ensure name is lowercase
    $name = strtolower($name);
    // do_action( 'qm/debug', $name );

    // Determine single and plural values from $name
    $single = singularize($name);
    $plural = pluralize($name);
    $unique_plural = unique_pluralize($name, $single);

    // Fill out these values if neccessary
    if ( (!isset($names['label_single'])) and (isset($names['label'])) ) $names['label_single'] = singularize( $names['label'] );
    if ( (!isset($names['label_plural'])) and (isset($names['label'])) ) $names['label_plural'] = pluralize( $names['label'] );
    if ( (!isset($names['slug_single']))  and (isset($names['slug']))  ) $names['slug_single']  = singularize( $names['slug'] );
    if ( (!isset($names['slug_plural']))  and (isset($names['slug']))  ) $names['slug_plural']  = pluralize( $names['slug'] );
    if ( (!isset($names['camel_single'])) and (isset($names['camel'])) ) $names['camel_single'] = singularize( $names['camel'] );
    if ( (!isset($names['camel_plural'])) and (isset($names['camel'])) ) $names['camel_plural'] = unique_pluralize( $names['camel'] );

    // Return $names object
    return [

      // foo_bar
      'key_single'   => underscore( $names['key_single'] ?? $single ),
      
      // foo_bars
      'key_plural'   => underscore( $names['key_plural'] ??  $unique_plural ),

      // Foo Bar
      'label_single' => $names['label_single'] ?? titleize( $single ),

      // Foo Bars
      'label_plural' => $names['label_plural'] ?? titleize( $plural ),

      // foo-bar
      'slug_single'  => hyphenate( $names['slug_single'] ?? $single ),

      // foo-bars
      'slug_plural'  => hyphenate( $names['slug_plural'] ?? $plural ),

      // fooBar
      'camel_single' => camelize( ( $names['camel_single'] ?? $single ), true ),
    
      // fooBars
      'camel_plural' => camelize( ( $names['camel_plural'] ?? $unique_plural ), true ),

      // \nf\FooBar
      'class'        => $names['class'] ?? '\\' . __NAMESPACE__ . '\\' . camelize( $single, false ),
    
    ];

  }



  private static function register_custom_post_type() {

    $names = static::$names;
    $props = static::$props;

    // Args detailed in this gist:
    // https://github.com/johnbillion/extended-cpts
    register_extended_post_type( $names['key_single'], 
      
      array_merge([

        # https://gist.github.com/justintadlock/6552000
        'supports'        => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields'],
        'has_archive'     => $props['has_archive'],
        'query_var'       => $names['slug_single'],
        'capability_type' => [$names['key_single'], $names['key_plural']],
        'map_meta_cap'    => true,
        'show_in_rest'    => true,
        'rest_base'       => $names['slug_plural'],
        'taxonomies'      => array_keys($props['taxonomies']),

        # https://docs.wpgraphql.com/getting-started/custom-post-types/
        'show_in_graphql'     => true,
        'graphql_single_name' => $names['camel_single'],
        'graphql_plural_name' => $names['camel_plural'],

        // 'rewrite' => [
        //   'slug' => $names['slug_plural'],
        // ],

        # Show all posts on the post type archive
        // 'archive' => [
        //   'posts_per_page' => 10, 
        //   'orderby' => 'date', 
        //   'order' => 'DESC',
        //   'nopaging' => true,
        // ],

        # https://github.com/johnbillion/extended-cpts/wiki/Custom-permalink-structures
        'rewrite' => [
        //   'permastruct' => '/' . $names['slug_plural'] . '/%year%/%monthnum%/%' . $names['key_single'] . '%',
        ],
        
        # https://github.com/johnbillion/extended-cpts/wiki/Query-vars-for-sorting
        'site_sortables' => [
        //   'start_date' => [ 'meta_key'   => 'start_date', 'default' => 'DESC' ],
        //   'my_genre'   => [ 'taxonomy'   => 'genre' ],
        //   'updated'    => [ 'post_field' => 'post_modified_gmt', ],
        ],

        # https://github.com/johnbillion/extended-cpts/wiki/Query-vars-for-filtering
        'site_filters' => [
          // 'my_foo'      => [ 'meta_key' => 'foo' ],
          // 'my_genre'    => [ 'taxonomy' => 'genre' ],
          // 'my_bar'      => [ 'meta_key' => 'bar', 'meta_query' => [ 'compare' => '>', 'type' => 'NUMERIC' ] ],
          // 'my_foobar'   => [ 'meta_search_key' => 'foobar' ],
          // 'complete'    => [ 'meta_exists' => 'complete' ],
          // 'help_needed' => [ 'meta_key_exists' => 'help_needed' ],
          // 'my_genre'    => [ 'taxonomy' => 'genre' ],
          // 'my_filter'   => [ 'meta_key' => 'foo', 'cap' => 'manage_options' ],
        ],

        # https://github.com/johnbillion/extended-cpts/wiki/Other-admin-parameters
        // 'enter_title_here'   => 'Title',
        // 'featured_image'     => 'Post Image',
        // 'quick_edit'         => false,
        // 'dashboard_glance'   => false,
        // 'dashboard_activity' => true,
        // 'block_editor'       => false,
        // 'show_in_feed'       => true,

      ], static::$args), [

        'singular' => $names['label_single'],
        'plural'   => $names['label_plural'],
        'slug'     => $names['slug_plural'],

    ]);

    // Remove supports features using $args['unsupports']
    foreach ( (static::$args['unsupports'] ?? []) as $feature ) {
      remove_post_type_support( $names['key_single'], $feature );
    }

  }


  private static function register_custom_meta_boxes() {

    $names = static::$names;
    $props = static::$props;

    add_action( 'cmb2_admin_init', function() use( $names, $props ) {
      foreach( $props['metaboxes'] as $metabox ) {

        $metabox['title'] ??= $names['label_single'];
        $metabox['context'] ??= 'side';
        $metabox['id'] ??= $names['key_single'] .'_'. $metabox['context'];
        $metabox['fields'] ??= [];

        if ( count($metabox['fields']) > 0 ) {
        
          $cmb = new_cmb2_box([
            'id'            => underscore($metabox['id']),
            'title'         => $metabox['title'],
            'object_types'  => [$names['key_single']],
            'context'       => $metabox['context'],
            'priority'      => $metabox['priority'] ?? 'high',
            'show_names'    => $metabox['true'] ?? true,
          ]);

          foreach($metabox['fields'] as $field) {
            $field['id'] ??= underscore($field['name']);
            $field['name'] ??= titleize($field['id']);
            $cmb->add_field($field);
          }
        
        }
      }

    }, 20);
  }


  private static function register_allowed_block_types() {

    $names = static::$names;
    $props = static::$props;

    add_filter( 'allowed_block_types', function( $allowed_block_types, $post ) use( $names, $props ) {
      if ( $post->post_type === $names['key_single'] ) {

        if (is_array($props['blocks'])) {
          $types = [];
          foreach($props['blocks'] as $type) {
            $types[] = $type;
          };
          return $types;

        } else {
          return true;
        }

      }
      return $allowed_block_types;
    }, 10, 2 );

  }

  // Add a block category named after this post type
  private static function register_block_categories() {
    $names = static::$names;
    add_filter( 'block_categories', function( $categories, $post ) use( $names ) {
      return array_merge( $categories, [
        [ 'slug' => $names['slug_single'], 'title' => $names['label_single'] ],
      ]);
    }, 10, 2);
  }

  private static function register_native_post_type() {

    $post_type = get_post_type_object( static::$name );
    $post_type->template = static::$args['template'] ?? null;
    $post_type->template_lock = static::$args['template_lock'] ?? false;

    // Add supports features
    // add_post_type_support( static::$name, static::$args['supports'] );

    // Remove supports features using $args['unsupports']
    foreach ( (static::$args['unsupports'] ?? []) as $feature ) {
      remove_post_type_support( static::$name, $feature );
    }

  }


  private static function register_taxonomies() {
    $post_type = static::$name;

    foreach(static::$props['taxonomies'] as $name => $args) {

      if ( ( $name == 'category' ) || ( $name == 'tag' ) ) {
        register_taxonomy_for_object_type( $name, $post_type );
       continue;
      }

      $tax_args = (is_array($args)) ? $args : [];
      $tax_names = static::generate_names( $name, $tax_args['names'] ?? [] );
      $tax_name = $tax_names['key_single']; // update $name arg in case it got modified 

      # https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
      register_extended_taxonomy( $tax_name, $post_type, array_merge([

        # Default $args
        'public'            => true,  
        'show_ui'           => true,  
        'hierarchical'      => true,  
        'query_var'         => $tax_names['slug_single'],
        'exclusive'         => false, # Custom arg  // true means: just one can be selected  
        'allow_hierarchy'   => false, # Custom arg  //  
        'meta_box'          => null,  # Custom arg  // can be null, 'simple', 'radio', 'dropdown' -> 'radio' and 'dropdown' just allow exclusive choices (will overwrite the set choise), simple has exclusive and multi options  
        'dashboard_glance'  => false, # Custom arg  // show or not on dashboard glance  
        'checked_ontop'     => null,  # Custom arg  //   
        'admin_cols'        => null,  # Custom arg  // added admin columns  
        'required'          => false, # Custom arg  //

        # https://docs.wpgraphql.com/getting-started/custom-taxonomies/
        'show_in_graphql'     => true,
        'graphql_single_name' => $tax_names['camel_single'],
        'graphql_plural_name' => $tax_names['camel_plural'],

      ], $tax_args), [

        'singular' => $tax_names['label_single'],
        'plural'   => $tax_names['label_plural'],
        'slug'     => $tax_names['slug_plural'],

      ]);

    }

  }


  private static function register_post_meta() {
    $post_type = static::$name;

    foreach( static::$props['meta'] as $meta_key => $meta_args ) {

      $meta_key = underscore($meta_key);
      $meta_args = (is_array($meta_args)) ? $meta_args : [];

      register_post_meta( $post_type, $meta_key, array_merge([
      
        'show_in_rest' => true,
        'type' => 'string', // string, boolean, integer, number, array, object
        'single' => true,

        'sanitize_callback' => function( $meta_value, $meta_key, $meta_type ) {
          switch ($meta_type) {
            case "string":  return (string) $meta_value;
            case "boolean": return (bool) $meta_value;
            case "integer": return (int) $meta_value;
            case "number":  return (float) $meta_value;
            case "array":   return (array) $meta_value;
            case "object":  return (object) $meta_value;
          }
          return $meta_value;
        },

        'auth_callback' => function( $allowed, $meta_key, $post_id, $user_id, $cap, $caps ) {
          $post = get_post( $post_id );
          $cpt = get_post_type_object( $post->post_type );
          return current_user_can( $cpt->cap->edit_posts );
        }
      
      ], $meta_args ) );
    }

  }


  private static function is_native_post_type() {
    if ( ( static::$name == 'post' ) or ( static::$name == 'page' ) ) {
      return true;
    } else {
      return false;
    }
  }

  // Default permissions for the default roles for this custom post type
  private static function roles_caps(): array {

    $all_caps = [
      'create',
      'edit',
      'edit_others',
      'publish',
      'read_private',
      'delete',
      'delete_private',
      'delete_published',
      'delete_others',
      'edit_private',
      'edit_published',
    ];

    $most_caps = [
      'edit',
      'publish',
      'delete',
      'delete_published',
      'edit_published',
    ];

    $few_caps = [
      'edit',
      'delete',
    ];

    return [
      'administrator' => $all_caps,
      'editor'        => $all_caps,
      'author'        => $most_caps,
      'contributor'   => $few_caps,
    ];

  }


  public static function activate( $force = false ) {

    // If this wordpress site isn't installed yet, bail
    if ( ! is_blog_installed() ) return;

    // Post Type in single and plural
    $key_single = static::$names['key_single'];
    $key_plural = static::$names['key_plural'];

    // Check if this has been activated before
    $isnt_activated = ( '1' !== get_option( "nf_{$key_single}_activated" ) ) ? true : false;

    // Activate if it hasn't, or if being forced to
    if ( ($isnt_activated) or ($force) ) {

      // Add default caps to default roles
      foreach( static::roles_caps() as $role_type => $cap_types ) {
        $role = get_role( $role_type );

        foreach( $cap_types as $cap_type ) {
          $role->add_cap( "${cap_type}_${key_plural}" );
        }
      }

      // Update rewrite database
      flush_rewrite_rules(false);

    }

    // Set this as activated now
    if ($isnt_activated) {
      update_option( "nf_{$key_single}_activated", '1' );
    }

  }


  public static function activate_all() {
    foreach(self::$post_types as $post_type) { 
      update_option( "nf_{$post_type}_activated", '0' );
    }
  }

}
