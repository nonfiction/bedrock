// This is contains JavaScript for blocks (shared by front-end and editor)
// WP hook: enqueue_block_assets (in footer)

// Import all ./site/src/blocks/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/src/blocks', true, /\/script\.js$/));
