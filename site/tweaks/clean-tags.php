<?php // Clean up the extra stuff WP adds to certain tags
namespace nf;

// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;


// Clean up output of stylesheet <link> tags
add_filter('style_loader_tag', function($input){
  preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
  if (empty($matches[2])) {
    return $input;
  }
  // Only display media if it is meaningful
  $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
});


// Clean up output of <script> tags
add_filter('script_loader_tag', function($input){
  $input = str_replace("type='text/javascript' ", '', $input);
  $input = \preg_replace_callback(
    '/document.write\(\s*\'(.+)\'\s*\)/is',
    function ($m) {
      return str_replace($m[1], addcslashes($m[1], '"'), $m[0]);
    },
    $input
  );
  return str_replace("'", '"', $input);
});


// <img /> <input />
foreach(['get_avatar', 'comment_id_fields', 'post_thumbnail_html'] as $filter) {
  add_filter($filter, function($input) {
    return str_replace(' />', '>', $input);
  });
}
