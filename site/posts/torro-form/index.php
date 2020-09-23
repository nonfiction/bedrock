<?php
namespace nf;
use \Timber\Timber;
use \Twig\TwigFunction;
use \Twig\TwigFilter;


add_filter( 'register_post_type_args', function( $args, $post_type) {
  if ( 'torro_form' === $post_type ) {
    $args['show_in_rest'] = true;
  }
  return $args;
}, 10, 2 );

add_filter( 'torro_container_partials_before', function() {
  echo '<div class="nf-form__fields">';
});

add_filter( 'torro_container_partials_after', function() {
  echo '</div>';
});

add_filter( 'timber/twig', function( $twig ) {

  $twig->addFunction( new TwigFunction( 'torro_form', function( $id = false ) {
    if ($id) {
      return do_shortcode("[torro_form id=\"${id}\"]");
    }
  }));

  $twig->addFilter( new TwigFilter( 'torro_form', function( $id = false ) {
    if ($id) {
      return do_shortcode("[torro_form id=\"${id}\"]");
    }
  }));

  return $twig;

});
