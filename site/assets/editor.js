// This is contains JavaScript for blocktypes (editor)
// WP hook: enqueue_block_editor_assets

// Initialize nf.blockTypes object
global.nf = global.nf || {};
nf.blockTypes = nf.blockTypes || {};

// Method to add new blocktypes
nf.registerBlockType = function(name, blockType){
  nf.blockTypes[name] = blockType;
}

// Method to load a stored blocktype
nf.loadBlockType = function(name){
  if ( nf.blockTypes.hasOwnProperty(name) ) {
    let blockType = nf.blockTypes[name];
    wp.blocks.registerBlockType(name, blockType);
  }
}

// Import all ./site/src/blocks/*/index.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/src/blocks', true, /\/index\.js$/));

// // Import all ./blocks/*/*.js files (where the filename matches the directory)
// function importAll (r) { r.keys().forEach(r) }
// importAll(require.context('/srv/web/app/site/src/blocks', true, /\/(.*)\/\1\.js/));
