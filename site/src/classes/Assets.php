<?php

namespace nf;

class Assets {

  public $src = [];

  public function __construct( $manifest_path ) {
    $this->load( $manifest_path );
    $this->do_admin();
    $this->do_theme();
    $this->do_blocks();
    $this->do_blocktypes();
    // $this->dequeue_wp_block_css();
  }


  // Load the manifest.json to get paths to compiled assets
  private function load( $manifest_path ) {

    $manifest = json_decode( file_get_contents( $manifest_path ) );

    $this->src['nf-head-js']        = $manifest->head->js        ?? null;
    $this->src['nf-theme-css']      = $manifest->theme->css      ?? null;
    $this->src['nf-theme-js']       = $manifest->theme->js       ?? null;

    $this->src['nf-admin-css']      = $manifest->admin->css      ?? null;
    $this->src['nf-admin-js']       = $manifest->admin->js       ?? null;

    $this->src['nf-blocks-css']     = $manifest->blocks->css     ?? null;
    $this->src['nf-blocks-js']      = $manifest->blocks->js      ?? null;

    $this->src['nf-blocktypes-css'] = $manifest->blocktypes->css ?? null;
    $this->src['nf-blocktypes-js']  = $manifest->blocktypes->js  ?? null;

  }


  // Theme styles and scripts
  private function do_theme() {
    add_action('wp_enqueue_scripts', function() {

      $this->enqueue([ 'handle' => 'nf-head-js' ]);
      $this->enqueue([ 'handle' => 'nf-theme-css' ]);
      $this->enqueue([ 'handle' => 'nf-theme-js', 'in_footer' => true ]);

    },100);
  }


  // Admin styles and scripts
  private function do_admin() {
    add_action('admin_enqueue_scripts', function( $hook ) {

      // This breaks these admin pages, so skip them
      if ( in_array( $hook, ['nav-menus.php'] ) ) {
        return;
      }

      $this->enqueue([ 'handle' => 'nf-admin-css' ]);
      $this->enqueue([ 'handle' => 'nf-admin-js', 'in_footer' => true ]);

    },100);
  }


  // Blocks styles and scripts (both theme and admin)
  private function do_blocks() {
    add_action('enqueue_block_assets', function() {

      $this->enqueue([ 'handle' => 'nf-blocks-css', 'deps' => ['wp-editor'] ]);
      $this->enqueue([ 'handle' => 'nf-blocks-js', 'in_footer' => true ]);

    },100);
  }


  // BlockTypes (admin only)
  private function do_blocktypes() {
    add_action('enqueue_block_editor_assets', function() {

      $this->enqueue([ 'handle' => 'nf-blocktypes-css', 'deps' => ['wp-edit-blocks'] ]);
      $this->enqueue([ 'handle' => 'nf-blocktypes-js', 'deps' => [
        'wp-blocks',
        'wp-components',
        'wp-editor',
        'wp-element',
        'wp-i18n',
        'wp-data', 
        'wp-api',
        'wp-edit-post', 
        // 'wp-plugins', 
      ] ]);

    },100);
  }


  // // Dequeue WP's block CSS -- this gets added in by Webpack instead
  // private function dequeue_wp_block_css() {
  //   add_action('wp_enqueue_scripts', function() {
  //
  //     return;
  //     wp_dequeue_style( 'wp-block-library' );
  //     wp_dequeue_style( 'wp-block-library-theme' );
  //
  //   },100);
  // }


  // Universal register/enqueue function
  private function register( $args=[], $enqueue = false ) {

    // Stop if these are missing
    $handle  = $args['handle'] ?? '';
    if ( empty($handle) ) return false;

    $src = $args['src'] ?? $this->src[$handle] ?? '';
    if ( empty($src) ) return false;

    // Extract variables from object array
    $src     = home_url() . $src;
    $deps    = $args['deps'] ?? [];
    $ver     = $args['ver'] ?? null;

    // Adding CSS
    if ( substr_compare($src, '.css', -4, 4) === 0 ) {

      $media = $args['media'] ?? 'all';
      wp_register_style( $handle, $src, $deps, $ver, $media );
      if ( $enqueue ) wp_enqueue_style( $handle );

      // Adding JS
    } else {

      $in_footer = $args['in_footer'] ?? false;
      wp_register_script( $handle, $src, $deps, $ver, $in_footer );
      if ( $enqueue ) wp_enqueue_script( $handle );

    }
  }


  // Universal enqueue function
  private function enqueue( $args=[] ) {
    $this->register( $args, true );
  }

}
