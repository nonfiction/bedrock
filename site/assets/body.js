// This is contains JavaScript for the theme that loads before the </body>
// WP hook: wp_enqueue_scripts (in footer)

// Catch any SVG's that were missed
SVGInject(document.querySelectorAll('img[src$="svg"]'));

// Import npm modules
import $ from 'jquery'

// Expose function to global scope
// window.$ = $

// import 'slick-carousel'
import ScrollTrigger from '@terwanerik/scrolltrigger'

// Wait until dom-ready
$(document).ready(function(){

  const trigger = new ScrollTrigger();
  trigger.add( '#top', { toggle: { callback: {
    in:  (trigger) => $('.navbar').removeClass('navbar--scroll'),
    out: (trigger) => $('.navbar').addClass('navbar--scroll'),
  }}} );

});
