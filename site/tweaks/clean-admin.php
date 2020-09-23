<?php
namespace nf;

add_intervention( 'remove-howdy' );
add_intervention( 'remove-help-tabs' );
add_intervention( 'remove-update-notices', 'all-not-admin' );
// add_intervention( 'remove-toolbar-frontend', ['all-not-admin'] );
add_intervention( 'remove-toolbar-items', ['logo', 'updates', 'comments', 'new-user', 'themes', 'customize'], 'all' );
add_intervention( 'remove-user-fields', ['options', 'names', 'contact'], ['editor', 'author'] );
add_intervention( 'remove-user-roles', ['subscriber', 'contributor'] );
add_intervention( 'remove-page-components', [ 'author', 'custom-fields', 'comments', 'trackbacks' ]);
add_intervention( 'remove-post-components', [ 'custom-fields', 'comments', 'trackbacks' ]);
// add_intervention( 'add-dashboard-redirect', 'admin.php?page=wp_stream', 'all' );
add_intervention( 'remove-widgets' );
add_intervention( 'update-pagination', 100 );
