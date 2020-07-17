// This is contains JavaScript for the theme that loads before the </body>
import './theme.css';

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

// Catch any SVG's that were missed
SVGInject(document.querySelectorAll('img[src$="svg"]'));

// import $ from 'jquery'
// import 'slick-carousel'
//
// import ScrollTrigger from '@terwanerik/scrolltrigger'
// const trigger = new ScrollTrigger();
//
//
// $(document).ready(function(){
//
//   trigger.add( '#top', { toggle: { callback: {
//     in:  (trigger) => $('.navbar').removeClass('navbar--scroll'),
//     out: (trigger) => $('.navbar').addClass('navbar--scroll'),
//   }}} );
//
//   $('.navbar__hamburger button').click(function(e){
//     e.preventDefault();
//     $(this).toggleClass('is-active');
//     $('.navbar__overlay').toggleClass('navbar__overlay--is-active');
//     window.scrollTo(0,0);
//   });
//
// });
