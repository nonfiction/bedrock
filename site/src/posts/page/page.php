<?php
namespace nf;

PostType::register_post_type( 'page', [
  'blocks' => true, // allow all blocks
  'template' => [],
  'template_lock' => false,

  // CMB2 custom fields (post meta)
  // https://github.com/CMB2/CMB2/wiki/Field-Types
  'fields' => [
    [
      'id'           => 'hero_1',
      'name'         => 'Hero Image 1',
      'desc'         => 'Main Hero Image',
      'type'         => 'file',
      'options'      => [ 'url' => true ],
      'text'         => [ 'add_upload_file_text' => 'Add or Upload Image'  ],
      'query_args'   => [ 'type' => [ 'image/gif', 'image/jpeg', 'image/png', ]],
      'preview_size' => 'large', 
    ],
    [
      'id'           => 'hero_2',
      'name'         => 'Hero Image 2',
      'desc'         => 'Additional Hero Image',
      'type'         => 'file',
      'options'      => [ 'url' => true ],
      'text'         => [ 'add_upload_file_text' => 'Add or Upload Image'  ],
      'query_args'   => [ 'type' => [ 'image/gif', 'image/jpeg', 'image/png', ]],
      'preview_size' => 'large', 
    ],
    [
      'id'           => 'hero_3',
      'name'         => 'Hero Image 3',
      'desc'         => 'Additional Hero Image',
      'type'         => 'file',
      'options'      => [ 'url' => true ],
      'text'         => [ 'add_upload_file_text' => 'Add or Upload Image'  ],
      'query_args'   => [ 'type' => [ 'image/gif', 'image/jpeg', 'image/png', ]],
      'preview_size' => 'large', 
    ],
  ],

]);


function format_page($post) {
  // $post->title = 'nf-page: ' . $post->title;
  return $post;
}

