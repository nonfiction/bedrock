import json from './block.json';
import classnames from 'classnames';

const { registerBlockType } = nf;
const { serverSideRender: ServerSideRender } = wp;
const { registerBlockStyle } = wp.blocks;
const { compose, withState } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { Button, TextControl, PanelBody, SelectControl, Toolbar, } = wp.components;
const { BlockControls, BlockIcon, ColorPalette, InnerBlocks, InspectorControls, MediaPlaceholder, MediaReplaceFlow, MediaUpload, MediaUploadCheck, PlainText, RichText, withColors, } = wp.blockEditor;

// Create custom Component to help choose which post
const TermChooser = compose(

  // SELECT
  withSelect( (select, props) => {
    return {
      terms: select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1 })
    };
  }),

  // DISPATCH
  withDispatch( (dispatch, props) => {
    return {};
  }),

// COMPONENT
)( (props) => {

  let { terms, value, onChange } = props;

  let options = (!terms) ? [] : terms.map( (term) => ({ 
    label: term.name, 
    value: term.slug,
  }) );

  options.unshift({ label: 'None', value: 0});

  return ( 
    <SelectControl value={ value } options={ options } onChange={ onChange } />
  );

}); 



registerBlockType( json, { 

  edit: (props) => {
    let { attributes, setAttributes, className } = props;
    let classes = classnames( className, 'editor', 'nf-tabbed-posts' );

    return (<>

      <div className={ classes }>
        <ServerSideRender block="nf/tabbed-posts" attributes={ attributes } />
      </div>

      <InspectorControls>
        <PanelBody title="Settings">
          <PlainText className="nf--is-bordered"
            value={ attributes.heading } 
            onChange={ ( value ) => setAttributes( { heading: value } ) } 
            placeholder={ 'Type Heading...' } 
          />
          <PlainText className="nf--is-bordered nf--is-tall"
            value={ attributes.content } 
            onChange={ ( value ) => setAttributes( { content: value } ) } 
            placeholder={ 'Type Content...' } 
          />
          <TermChooser 
            value={ attributes.term }
            onChange={ ( value ) => setAttributes({ term: value }) }
          />
        </PanelBody>
      </InspectorControls>

    </>)
  },


  save: (props) => null,

});
