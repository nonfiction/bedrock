const { registerBlockStyle, unregisterBlockStyle } = wp.blocks;

wp.domReady(function(){
  unregisterBlockStyle( 'core/button', 'outline' );
  console.log('unregistered');
});

registerBlockStyle( 'core/button', [
  {
    name: 'download',
    label: 'Download',
  }
]);

