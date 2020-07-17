// This is contains JavaScript for the theme that loads in the <head>
// console.log("Loaded head.js");

import '@iconfu/svg-inject';

window.SVGInject.setOptions({
  afterInject: function(img, svg) {
    svg.removeAttribute('width');
    svg.removeAttribute('height');
  }
});
