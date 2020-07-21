// Import all ./posts/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('../posts', true, /\/script\.js$/));
