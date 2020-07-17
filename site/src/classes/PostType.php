<?php
namespace nf;

use function \ICanBoogie\pluralize;
use function \ICanBoogie\singularize;
use function \ICanBoogie\underscore;
use function \ICanBoogie\hyphenate;
use function \ICanBoogie\humanize;
use function \ICanBoogie\titleize;

function underscored($string) {
 return underscore( str_replace( '/', '_', $string ) );
}


class PostType {

  public static $post_types = [];

  public static function register_post_type( $name, $args = [] ) {
    $post_type = new PostType( $name, $args );
    self::$post_types[$post_type->post_type] = $post_type;
    // self::$post_types[$name] = new PostType( $name, $args );
  }

  public static function slug( $post_type_name = false ) {
    if ($post_type_name) {
      return singularize( self::$post_types[$post_type_name]->slug ?? $post_type_name );
    }
  }

  public static function slugs( $post_type_name = false ) {
    if ($post_type_name) {
      return pluralize( self::$post_types[$post_type_name]->slug ?? $post_type_name );
    }
  }


  public static function activate_all() {
    foreach(self::$post_types as $post_type) {
      $post_type->activate(false);
    };
    flush_rewrite_rules(false);
  }

  public static function deactivate_all() {
    foreach(self::$post_types as $post_type) {
      $post_type->deactivate(false);
    };
    flush_rewrite_rules(false);
  }


  public $prefix = false;
  public $name = null;
  public $type = [];
  public $title = [];
  public $post_type = null;
  public $wp_post_type = null;
  public $slug = null;
  public $icon = 'dashicons-format-status';
  public $taxonomies = [];


  public function __construct( $name, $args = [] ) {

    // NAME / PREFIX / TYPE
    // --------------------------------------------------
    // => nf/foo-bar (name)
    // => nf         (prefix)
    // => foo_bar    (type singular)
    // => foo_bars   (type plural)

    // Start with name from param and false prefix
    // $this->name = strtolower( str_replace( [' ', '-'], '_', $name ) );
    $this->name = underscore( $name );
    $this->prefix = false;
    $this->type = [$this->name, $this->name];

    // If there's a slash, reassign the prefix and singular/plural
    if ( strpos( $this->name, '/' ) !== false ) {
      [$this->prefix, $this->type[0]] = explode( '/', $this->name );
      $this->type[1] = $this->type[0];
    }

    // Assign singular and plural
    $this->type[0] = singularize( $this->type[0] );
    $this->type[1] = pluralize( $this->type[1] );


    // PREFIX
    // --------------------------------------------------
    // => nf

    // Allow args prefix to override
    if ( isset( $args['prefix'] )) {
      $this->prefix = underscored( $args['prefix'] );
      unset($args['prefix']);
    }


    // TYPE
    // --------------------------------------------------
    // => foo_bar
    // => foo_bars

    // Allow args name to ovverride
    if ( isset( $args['type'] )) {

      // If it's an array, use the first for singular and the second for plural
      if ( is_array( $args['type'] ) ) {
        // list( $singular, $plural ) = $args['type'];
        [$singular, $plural] = $args['type'];

      // Otherwise, assign singular and plural with appended 's'
      } else {
        $singular = singularize($args['type']);
        $plural = pluralize($args['type']);
      }

      $this->type = [
        underscored($singular),
        underscored($plural),
      ];

      unset($args['type']);
    }

    // Make double-sure singular and plural are different
    if ($this->type[0] == $this->type[1]) {
      $this->type[1] .= 's';
    }


    // POST TYPE
    // --------------------------------------------------
    // => nf_foo_bar

    // Post type is prefix + singular type
    $this->post_type = ( ($this->prefix) ? $this->prefix . '_' : '' ) . $this->type[0];


    // TITLE
    // --------------------------------------------------
    // => Foo Bar
    // => Foo Bars

    // Assign singular and plural with appended 's'
    $this->title = [
      // ucwords( str_replace( '_', ' ', $this->type[0] ) ),
      // ucwords( str_replace( '_', ' ', $this->type[1] ) ),
      titleize( $this->type[0] ),
      titleize( $this->type[1] ),
    ];

    // Allow args name to ovverride
    if ( isset( $args['title'] )) {

      // If it's an array, use the first for singular and the second for plural
      if ( is_array( $args['title'] ) ) {
        // list( $singular, $plural ) = $args['title'];
        [$singular, $plural] = $args['title'];

      // Otherwise, assign singular and plural with appended 's'
      } else {
        $singular = singularize($args['title']);
        $plural = pluralize($args['title']);
      }

      $this->title = [ $singular, $plural ];
      unset($args['title']);
    }


    // SLUG
    // --------------------------------------------------
    // => foo-bars
    $this->slug = $args['slug'] ?? $this->title[1];
    // $this->slug = strtolower( str_replace( ' ', '-', $this->slug ) );
    $this->slug = hyphenate( $this->slug );
    unset($args['slug']);


    // Assign icon
    $this->icon = $args['icon'] ?? 'dashicons-format-status';
    $this->icon = 'dashicons-' . str_replace( 'dashicons-', '', $this->icon );
    unset($args['icon']);


    // Assign blocks
    $this->blocks = $args['blocks'] ?? $this->default_blocks();
    unset($args['blocks']);

    // If first block in array is 'default', merge in a default set of blocks
    if (is_array( $this->blocks) ) {
      if ($this->blocks[0] === 'default') {
        unset($this->blocks[0]);
        $this->blocks = array_merge($this->default_blocks(), $this->blocks);
      }
    }


    // Assign template
    $this->template = $args['template'] ?? $this->default_template();
    unset($args['template']);
    $this->template_lock = $args['template_lock'] ?? $this->default_template_lock();
    unset($args['template_lock']);


    // https://developer.wordpress.org/reference/functions/post_type_supports/
    $this->supports = $args['supports'] ?? $this->default_supports();
    unset($args['supports']);

    // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
    $this->schema = $args['schema'] ?? $this->default_schema();
    unset($args['schema']);

    // https://github.com/CMB2/CMB2/wiki/Field-Types
    $this->fields = $args['fields'] ?? $this->default_fields();
    unset($args['fields']);

    // https://developer.wordpress.org/block-editor/developers/backward-compatibility/meta-box/
    $this->render_metabox_func = $args['render_metabox'] ?? function(){};
    unset($args['render_metabox']);

    $this->capabilities = $args['capabilities'] ?? $this->default_capabilities();
    unset($args['capabilities']);

    $this->roles_capabilities = $args['roles_capabilities'] ?? $this->default_roles_capabilities();
    unset($args['roles_capabilities']);

    // $this->labels = $args['labels'] ?? $this->default_labels();
    // unset($args['labels']);

    $this->labels = array_merge($this->default_labels(), ($args['labels'] ?? []) );
    unset($args['labels']);


    // Register any taxonomies
    $this->register_taxonomies([
      'categories' => $args['categories'] ?? [],
      'tags' => $args['tags'] ?? [],
    ]);
    unset($args['categories']);
    unset($args['tags']);


    // Finally register or customize the post type
    if ( $this->is_native_post_type() ) {
      $this->register_native( $args );
    } else {
      $this->register( $args );
    }

  }



  // => foo_bars
  // => edit_foo_bars
  public function cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : '';
    return strtolower( $cap . $this->type[1] );
  }

  // => foo_bar
  // => edit_foo_bar
  public function meta_cap_type( $cap = false ): string {
    $cap = ($cap) ? $cap . '_' : '';
    return strtolower( $cap . $this->type[0] );
  }

  public $render_metabox_func = false;
  public function render_metabox($post=null) {
    ($this->render_metabox_func)($post);
  }


  private function register($args = []) {

    // Args detailed in this gist:
    // https://gist.github.com/justintadlock/6552000
    $args = array_merge([

      'post_type'       => $this->post_type,
      'labels'          => $this->labels,
      'rewrite'         => ['slug' => $this->slug, 'with_front' => true],
      'menu_icon'       => $this->icon,
      'taxonomies'      => $this->taxonomies,
      'public'          => true,
      'has_archive'     => $this->slug,
      'query_var'       => hyphenate($this->type[0]),
      // 'has_archive'     => true,
      'hierarchical'    => false,
      'supports'        => $this->supports,
      'template'        => $this->template,
      'template_lock'   => $this->template_lock,
      'show_in_rest'    => ['schema' => $this->schema],
      'capability_type' => [$this->meta_cap_type(), $this->cap_type()],
      'capabilities'    => $this->capabilities,
      'map_meta_cap'    => true,

    ], $args);

    // dump($this->slug);


    add_action( 'init', function() use($args) {
      $this->wp_post_type = register_post_type( $this->post_type, $args );
    });

    $this->set_allowed_block_types();

    add_action( 'cmb2_admin_init', function() {
      $cmb = new_cmb2_box([
        'id'            => $this->post_type,
        'title'         => $this->title[0] . ' Details',
        'object_types'  => [$this->post_type],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
      ]);
      foreach($this->fields as $field) {
        $field['id'] = $field['id'] ?? strtolower(str_replace(' ', '_', $field['name']));
        $cmb->add_field($field);
      }
    });


    // Map meta-capabilities for this custom post type
    add_filter( 'map_meta_cap', [$this, 'map_meta_cap'], 10, 4 );

  }


  private function set_allowed_block_types() {
    add_filter( 'allowed_block_types', function( $allowed_block_types, $post ) {
      if ( $post->post_type === $this->post_type ) {

        if (is_array($this->blocks)) {
          $types = [];
          foreach($this->blocks as $type) {
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


  public function register_native($args) {
    add_action( 'init', function() use($args) {

      $post_type = get_post_type_object( $this->post_type );
      $post_type->template = $this->template;
      $post_type->template_lock = $this->template_lock;

      add_post_type_support( $this->post_type, $this->supports );

      $this->set_allowed_block_types();

      add_action( 'cmb2_admin_init', function() {
        $cmb = new_cmb2_box([
          'id'            => $this->post_type,
          // 'title'         => $this->title[0] . ' Fields',
          'title'         => 'Meta',
          'object_types'  => [$this->post_type],
          'context'       => 'normal',
          'priority'      => 'high',
          'show_names'    => true,
        ]);
        foreach($this->fields as $field) {
          $field['id'] = $field['id'] ?? strtolower(str_replace(' ', '_', $field['name']));
          $cmb->add_field($field);
        }
      });

    });
  }

  public function register_taxonomies($taxonomies) {
    add_action( 'init', function() use($taxonomies) {

      $prefix = ($this->is_native_post_type()) ? '' : $this->post_type . '_';
      $slug = ($this->is_native_post_type()) ? '' : $this->slug . '/';

      foreach($taxonomies['categories'] as $category) {

        $name = underscored( singularize($category) );
        $singular = titleize( $name );
        $plural = pluralize( $singular );

        $this->taxonomies[] = $prefix.$name;

        register_taxonomy( $prefix.$name, $this->post_type, [
          'hierarchical'      => true,
          'show_ui'           => true,
          'show_admin_column' => true,
          'show_in_rest'      => true,
          'query_var'         => true,
          'rewrite'           => [ 
            'slug'              => $slug.hyphenate( $name ),
            'with_front'        => false,
          ],
          'labels'            => [
            'name'              => __( "$plural"            , $this->prefix ),
            'singular_name'     => __( "$singular"          , $this->prefix ),
            'search_items'      => __( "Search $plural"     , $this->prefix ),
            'all_items'         => __( "All $plural"        , $this->prefix ),
            'parent_item'       => __( "Parent $singular"   , $this->prefix ),
            'parent_item_colon' => __( "Parent $singular:"  , $this->prefix ),
            'edit_item'         => __( "Edit $singular"     , $this->prefix ),
            'update_item'       => __( "Update $singular"   , $this->prefix ),
            'add_new_item'      => __( "Add New $singular"  , $this->prefix ),
            'new_item_name'     => __( "New $singular Name" , $this->prefix ),
            'menu_name'         => __( "$plural"            , $this->prefix ),
          ],
        ]);
      }

      foreach($taxonomies['tags'] as $tag) {

        $name = underscored( singularize($tag) );
        $singular = titleize( $name );
        $plural = pluralize( $singular );

        $this->taxonomies[] = $prefix.$name;

        register_taxonomy( $prefix.$name, $this->post_type, [
          'hierarchical'      => false,
          'show_ui'           => true,
          'show_admin_column' => true,
          'show_in_rest'      => true,
          'query_var'         => true,
          'update_count_callback' => '_update_post_term_count',
          'rewrite'           => [ 
            'slug'              => $slug.hyphenate( $name ),
            'with_front'        => false,
          ],
          'labels'            => [
            'name'                       => __( "$plural"                           , $this->prefix ),
            'singular_name'              => __( "$singular"                         , $this->prefix ),
            'search_items'               => __( "Search $plural"                    , $this->prefix ),
            'popular_items'              => __( "Popular $plural"                   , $this->prefix ),
            'all_items'                  => __( "All $plural"                       , $this->prefix ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( "Edit $singular"                    , $this->prefix ),
            'update_item'                => __( "Update $singular"                  , $this->prefix ),
            'add_new_item'               => __( "Add New $singular"                 , $this->prefix ),
            'new_item_name'              => __( "New $singular Name"                , $this->prefix ),
            'separate_items_with_commas' => __( "Separate $plural with commas"      , $this->prefix ) , 
            'add_or_remove_items'        => __( "Add or remove $plural"             , $this->prefix )  , 
            'choose_from_most_used'      => __( "Choose from the most used $plural" , $this->prefix )  , 
            'not_found'                  => __( "No $plural found."                 , $this->prefix )  , 
            'menu_name'                  => __( "$plural"                           , $this->prefix ),
          ],
        ]);
      }

    });
  }



  private function is_native_post_type() {
    if ( ( $this->post_type == 'post' ) or ( $this->post_type == 'page' ) ) {
      return true;
    } else {
      return false;
    }
  }


  // CMB2
  public $fields = [];
  private function default_fields() {
    return [];
  }

  public $template = [];
  private function default_template() {
    return [];
  }

  public $template_lock = false;
  private function default_template_lock() {
    return false;
  }

  // http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
  public function map_meta_cap( $caps, $cap, $user_id, $args ): array {

    $edit_post   = $this->meta_cap_type('edit');
    $delete_post = $this->meta_cap_type('delete');
    $read_post   = $this->meta_cap_type('read');


    // If editing, deleting, or reading this cpt, get the post and post type object.
    if ( $edit_post == $cap || $delete_post == $cap || $read_post == $cap ) {
      $post = get_post( $args[0] );
      $post_type = get_post_type_object( $post->post_type );

      // Set an empty array for the caps.
      $caps = [];
    }

    // If editing this cpt, assign the required capability.
    if ( $edit_post == $cap ) {
      if ( $user_id == $post->post_author )
        $caps[] = $post_type->cap->edit_posts;
      else
        $caps[] = $post_type->cap->edit_others_posts;
    }

    // If deleting a post, assign the required capability.
    elseif ( $delete_post == $cap ) {
      if ( $user_id == $post->post_author )
        $caps[] = $post_type->cap->delete_posts;
      else
        $caps[] = $post_type->cap->delete_others_posts;
    }

    // If reading a private post, assign the required capability.
    elseif ( $read_post == $cap ) {

      if ( 'private' != $post->post_status )
        $caps[] = 'read';
      elseif ( $user_id == $post->post_author )
        $caps[] = 'read';
      else
        $caps[] = $post_type->cap->read_private_posts;
    }

    // Return the capabilities required by the user.
    return $caps;
  }

  public $blocks = true;
  private function default_blocks() {
    return true;
  }

  // Default labels for this custom post type
  public $labels = [];
  private function default_labels(): array {
    return [
      'name'                  => __( ''                  . $this->title[1] . ''                , $this->prefix ),
      'singular_name'         => __( ''                  . $this->title[0] . ''                , $this->prefix ),
      'menu_name'             => __( ''                  . $this->title[1] . ''                , $this->prefix ),
      'name_admin_bar'        => __( ''                  . $this->title[0] . ''                , $this->prefix ),
      'add_new'               => __( 'New '              . $this->title[0] . ''                , $this->prefix ),
      'add_new_item'          => __( 'Add New '          . $this->title[0] . ''                , $this->prefix ),
      'edit_item'             => __( 'Edit '             . $this->title[0] . ''                , $this->prefix ),
      'new_item'              => __( 'New '              . $this->title[0] . ''                , $this->prefix ),
      'view_item'             => __( 'View '             . $this->title[1] . ''                , $this->prefix ),
      'search_items'          => __( 'Search '           . $this->title[1] . ''                , $this->prefix ),
      'not_found'             => __( 'No '               . $this->title[1] . ' Found'          , $this->prefix ),
      'not_found_in_trash'    => __( 'No '               . $this->title[1] . ' found in Trash' , $this->prefix ),
      'all_items'             => __( 'All '              . $this->title[1] . ''                , $this->prefix ),
      'insert_into_item'      => __( 'Insert into '      . $this->title[0] . ''                , $this->prefix ),
      'uploaded_to_this_item' => __( 'Uploaded to this ' . $this->title[0] . ''                , $this->prefix ),
      'views'                 => __( 'Filter '           . $this->title[1] . ' list'           , $this->prefix ),
      'pagination'            => __( ''                  . $this->title[1] . ' list navigation', $this->prefix ),
      'list'                  => __( ''                  . $this->title[1] . ' list'           , $this->prefix ),
      'featured_image'        => __( 'Featured Image'                                          , $this->prefix ),
      'set_featured_image'    => __( 'Set featured image'                                      , $this->prefix ),
      'remove_featured_image' => __( 'Remove featured image'                                   , $this->prefix ),
      'use_featured_image'    => __( 'Use as featured image'                                   , $this->prefix ),
    ];
  }


  // Default permissions for the default roles for this custom post type
  public $roles_capabilities = [];
  private function default_roles_capabilities(): array {

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


  // List all the capability types for this custom post type
  public $capabilities = [];
  private function default_capabilities(): array {
    return [

      // meta caps (don't assign these to roles)
      'edit_post'              => $this->meta_cap_type('edit'),
      'read_post'              => $this->meta_cap_type('read'),
      'delete_post'            => $this->meta_cap_type('delete'),

      // primitive/meta caps
      'create_posts'           => $this->cap_type('create'),

      // primitive caps used outside of map_meta_cap()
      'edit_posts'             => $this->cap_type('edit'),
      'edit_others_posts'      => $this->cap_type('edit_others'),
      'publish_posts'          => $this->cap_type('publish'),
      'read_private_posts'     => $this->cap_type('read_private'),

      // primitive caps used inside of map_meta_cap()
      'read'                   => 'read',
      'delete_posts'           => $this->cap_type('delete'),
      'delete_private_posts'   => $this->cap_type('delete_private'),
      'delete_published_posts' => $this->cap_type('delete_published'),
      'delete_others_posts'    => $this->cap_type('delete_others'),
      'edit_private_posts'     => $this->cap_type('edit_private'),
      'edit_published_posts'   => $this->cap_type('edit_published'),

    ];
  }


  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public $schema = [];
  private function default_schema(): array {
    return [];
  }


  // Default list of features this custom post type suports
  public $supports = [];
  private function default_supports(): array {
    return ['title', 'editor', 'revisions', 'page-attributes', 'thumbnail', 'custom-fields'];
  }


  // https://make.wordpress.org/core/2019/10/03/wp-5-3-supports-object-and-array-meta-types-in-the-rest-api/
  public $scheme = [];


  // Call when plugin is activated
  public function activate( $flush = true ) {

    // Add default caps to default roles
    foreach($this->roles_capabilities as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->add_cap( $this->cap_type($cap_type) );
      }
    }

    // Make new URLs work
    if ( $flush ) {
      flush_rewrite_rules(false);
    }
  }


  // Call when plugin is deactivated
  public function deactivate( $flush = true ) {

    // Remove default caps from default roles
    foreach($this->roles_capabilities as $role_type => $cap_types) {
      $role = get_role($role_type);

      foreach($cap_types as $cap_type) {
        $role->remove_cap( $this->cap_type($cap_type) );
      }
    }

    // Remove old URLs
    if ( $flush ) {
      flush_rewrite_rules(false);
    }
  }

}
