<?php

namespace nf;
use \Timber;

class Page extends PostType {}

// Register Custom Post Type
add_action( 'init', function() {

  Page::register_post_type();


  // // Register block pattern
  // register_block_pattern( 'nf/internal-page', [
  //
  //   'title'       => 'Internal Page',
  //   'description' => 'Banner with an image sidebar',
  //   'categories' => ['text'],
  //   'keywords' => ['nf'],
  //   'content'     => (<<<END
  //
  //     <!-- wp:nf/banner {"heading":"Page Title","background_url":"/app/site/assets/img/banner.jpg"} /-->
  //
  //     <!-- wp:media-text {"mediaType":"image","mediaWidth":30,"verticalAlignment":"top"} -->
  //     <div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-top" style="grid-template-columns:30% auto"><figure class="wp-block-media-text__media"><img src="/app/site/assets/img/side.jpg" alt=""/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":4} -->
  //     <h4>My heading goes here</h4>
  //     <!-- /wp:heading -->
  //
  //     <!-- wp:paragraph -->
  //     <p>My paragraph goes here!</p>
  //     <!-- /wp:paragraph --></div></div>
  //     <!-- /wp:media-text -->
  //
  //   END),
  //
  // ] );

}, 18);
