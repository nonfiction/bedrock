import './style.css';
import $ from 'jquery';

$( '.nf-tabbed-posts__tabs a' ).click( function(e) {

  e.preventDefault();

  $( '.nf-tabbed-posts__tabs li' ).removeClass( 'is-active' );
  $(this).closest( 'li' ).addClass( 'is-active' );
  
  $( '.nf-tabbed-post' ).removeClass( 'is-active' );
  $( '.nf-tabbed-post-' + $(this).attr( 'rel' ) ).addClass( 'is-active' );

} );
