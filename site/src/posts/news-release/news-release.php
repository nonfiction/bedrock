<?php
namespace nf;

function format_news_release($post) {
  return $post;
}

PostType::register_post_type( 'news-release', [

  'icon' => 'admin-site',

  'categories' => [ 'Category' ],

  // CMB2 custom fields (post meta)
  // https://github.com/CMB2/CMB2/wiki/Field-Types
  'fields' => [
    [
      'id'   => 'id',
      'name' => 'Name',
      'desc' => 'Description',
      'type' => 'text',
    ],
  ],

]);

