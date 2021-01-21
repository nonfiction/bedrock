<?php
namespace nf;

add_filter( 'site_status_tests', function( $tests ) { 

  unset($tests['direct']['theme_version']); 
  unset($tests['async']['background_updates']); 
  return $tests;
  
} );
