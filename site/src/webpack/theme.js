// This is the theme entry file for webpack--do not edit!

import '../assets/theme.js';
import './posts-bundle.js';

if (module.hot) {
  module.hot.accept( '../assets/theme.js' );
  module.hot.accept( './posts-bundle.js' );
}
