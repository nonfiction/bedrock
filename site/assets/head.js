// This is contains JavaScript for the theme that loads in the <head>
// WP hook: wp_enqueue_scripts

// Import all ./site/src/posts/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/src/posts', true, /\/script\.js$/));

// Include the main stylesheet
import './style.css';




// Convert <img src="icon.svg" /> into <svg>...inline-svg-icon...</svg>
import '@iconfu/svg-inject';

window.SVGInject.setOptions({
  afterInject: function(img, svg) {
    svg.removeAttribute('width');
    svg.removeAttribute('height');
  }
});

// Create new HTML element from string
const el = ( domstring ) => {
  const html = new DOMParser().parseFromString( domstring , 'text/html');
  return html.body.firstChild;
};

// Append HTML string to selector
const append = ( selector, domstring) => {
  document.querySelectorAll( selector ).forEach( (parent) => {
    parent.appendChild( el(domstring) ); 
  });
}

// Prepend HTML string to selector
const prepend = ( selector, domstring ) => {
  document.querySelectorAll( selector ).forEach( (parent) => {
    parent.insertBefore( el(domstring), parent.childNodes[0] );
  });
}
