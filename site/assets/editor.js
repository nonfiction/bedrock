// This is contains JavaScript for blocktypes (editor)
// WP hook: enqueue_block_editor_assets

const { registerBlockType } = wp.blocks;

// Initialize nf object
global.nf = global.nf || {};

// Method to register new blocktypes with json and name in args
nf.registerBlockType = function( json = {}, override = {} ) {

  let args = { ...json, ...override };
  let name = args.name || false; 

  let icon = args.icon || "";
  args.icon = icon.replace('dashicons-', '');

  if ( name ) {
    registerBlockType( name, args );
  }

}

// Import block-related index.js files
// ./site/blocks/*/index.js
// ./site/posts/*/blocks/*/index.js
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/blocks', true, /^\.\/[\w\-]+\/index\.js$/));
importAll(require.context('/srv/web/app/site/posts', true, /^\.\/[\w\-]+\/blocks\/[\w\-]+\/index\.js$/));

// Custom scripting below
//
