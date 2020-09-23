<?php 
namespace nf;


// Skip in admin but allow in ajax
if ((is_admin() && !wp_doing_ajax())) return;

// Skip in sitemap
if (isset($_GET['sitemap'])) return;

// Skip login/register screens
if (in_array(($GLOBALS['pagenow'] ?? ''), ['wp-login.php', 'wp-register.php'])) return;


// Used by the method below
function make_link_relative($input) {

  // Will be comparing input to home url
  $site_url = parse_url(network_home_url());

  // Get URL from input
  $url = parse_url($input);
  $url['scheme'] = $url['scheme'] ?? $site_url['scheme'];

  // Leave feeds alone
  if (is_feed()) return $input;

  // Ensure it's a valid url
  if (!isset($url['host']) || !isset($url['path'])) return $input;

  // See if input url matches properly with home url
  $hosts_match = $site_url['host'] === $url['host'];
  $schemes_match = $site_url['scheme'] === $url['scheme'];
  $ports_exist = isset($site_url['port']) && isset($url['port']);
  $ports_match = ($ports_exist) ? $site_url['port'] === $url['port'] : true;

  // If so, return the relative version
  if ($hosts_match && $schemes_match && $ports_match) {
    return wp_make_link_relative($input);

    // If not, return as-is
  } else {
    return $input;
  }

}


// Relative URLs
foreach([
  'bloginfo_url',
  'the_permalink',
  'wp_list_pages',
  'wp_list_categories',
  'wp_get_attachment_url',
  'the_content_more_link',
  'the_tags',
  'get_pagenum_link',
  'get_comment_link',
  'month_link',
  'day_link',
  'year_link',
  'term_link',
  'the_author_posts_link',
  'script_loader_src',
  'style_loader_src',
  'theme_file_uri',
  'parent_theme_file_uri',
] as $tag) {
  add_filter($tag, 'nf\make_link_relative', 10, 1);
}

add_filter('wp_calculate_image_srcset', function ($sources) {
  foreach ((array) $sources as $source => $src) {
    $sources[$source]['url'] = make_link_relative($src['url']);
  }
  return $sources;
});

// Compatibility with The SEO Framework
add_action('the_seo_framework_do_before_output', function () {
  remove_filter('wp_get_attachment_url', 'nf\make_link_relative');
});

add_action('the_seo_framework_do_after_output', function () {
  add_filter('wp_get_attachment_url', 'nf\make_link_relative');
});
