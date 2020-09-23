const { registerBlockStyle } = wp.blocks;

registerBlockStyle( 'core/heading', [
  {
    name: 'default',
    label: 'Default',
    isDefault: true,
  },
  {
    name: 'decorative',
    label: 'Decorative',
    isDefault: false,
  }
]);
