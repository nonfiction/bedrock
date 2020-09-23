const { registerBlockStyle } = wp.blocks;

registerBlockStyle( 'core/columns', [
  {
    name: 'full',
    label: 'Full',
  },{
    name: 'narrow',
    label: 'Narrow',
  }
]);

registerBlockStyle( 'core/column', [
  {
    name: 'full',
    label: 'Full',
  }
]);
