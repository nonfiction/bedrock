// Import all ./blocks/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/src/blocks', true, /\/script\.js$/));
