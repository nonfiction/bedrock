// Import all ./posts/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('/srv/web/app/site/src/posts', true, /\/script\.js$/));
