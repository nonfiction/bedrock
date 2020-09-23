// This is contains JavaScript for the theme that loads before the </body>
// WP hook: wp_enqueue_scripts (in footer)

// Custom scripting below
//

import $ from 'jquery';

// Catch any SVG's that were missed
SVGInject(document.querySelectorAll('img[src$="svg"]'));

$(document).ready(function(){

  // DOM is ready...

});
