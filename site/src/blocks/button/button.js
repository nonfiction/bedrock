const { registerBlockStyle, unregisterBlockStyle } = wp.blocks;

// setTimeout(function(){
wp.domReady(function(){
  unregisterBlockStyle( 'core/button', 'outline' );
  console.log('unregistered');
});

// }, 5000);

registerBlockStyle( 'core/button', [
  {
    name: 'download',
    label: 'Download',
  }
]);

