// This is contains JavaScript for blocks (shared by front-end and editor)
// WP hook: enqueue_block_assets (in footer)

// Import block-related script.js files
// ./site/blocks/*/script.js
// ./site/posts/*/blocks/*/script.js
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/blocks', true, /^\.\/[\w\-]+\/script\.js$/));
importAll(require.context('/srv/web/app/site/posts', true, /^\.\/[\w\-]+\/blocks\/[\w\-]+\/script\.js$/));

// Custom scripting below
//
