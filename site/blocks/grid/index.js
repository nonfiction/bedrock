import json from './block.json';
import classnames from 'classnames';

const { registerBlockType } = nf;
const { serverSideRender: ServerSideRender } = wp;
const { registerBlockStyle } = wp.blocks;
const { compose, withState } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { Button, TextControl, PanelBody, SelectControl, Toolbar, } = wp.components;
const { BlockControls, BlockIcon, ColorPalette, InnerBlocks, InspectorControls, MediaPlaceholder, MediaReplaceFlow, MediaUpload, MediaUploadCheck, PlainText, RichText, withColors, } = wp.blockEditor;


registerBlockType( json, { 

  edit: (props) => {
    let { columns, width } = props.attributes;
    width = width || 'narrow';
    let classes = classnames( props.className, 'editor', 'wp-block-nf-grid', `is-${width}` );

    let options = [ 
      { label: 'Two',   value: '2' }, 
      { label: 'Three', value: '3' }, 
      { label: 'Four',  value: '4' },
    ]; 

    // <ColumnsChooser />
    const ColumnsChooser = () => { 
      function createControl( option ) {
        return {
          icon: `screenoptions`,
          title: `${ option.label } Columns`,
          isActive: columns === option.value,
          onClick: () => props.setAttributes( { columns: option.value } )
        };
      }

      return <Toolbar controls={ options.map(createControl) } />
    };

    return (<>
      <InspectorControls>
        <PanelBody title="Layout Settings">
          <SelectControl
            label='Number of Columns:'
            value={ columns }
            options={ options }
            onChange={ ( columns ) => { props.setAttributes( { columns } ); } }
          />
        </PanelBody>
      </InspectorControls>

      <BlockControls>
        <ColumnsChooser />
      </BlockControls>
        
      <div className={ classes } data-columns={ columns }>
        <InnerBlocks 
          orientation="horizontal" 
          templateLock={false} 
          renderAppender={() => <InnerBlocks.ButtonBlockAppender />} 
        />
      </div>
    </>)
  },


  save: (props) => {

    // let { columns, width } = props.attributes;
    let { columns } = props.attributes;
    // width = width || 'narrow';
    // let classes = classnames( props.className, 'wp-block-nf-grid', `is-${width}` );
    let classes = classnames( props.className, 'wp-block-nf-grid' );
    // let classes = classnames( props.className, 'wp-block-nf-grid' );

    return (
      <div className={ classes } data-columns={ columns }>
        <InnerBlocks.Content />
      </div>
    );

  },

});
