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
const PostChooser = compose(

  // SELECT
  withSelect( (select, props) => {
    return {
      posts: select( 'core' ).getEntityRecords( 'postType', 'torro_form' ),
    };
  }),

  // DISPATCH
  withDispatch( (dispatch, props) => {
    return {};
  }),

// COMPONENT
)( (props) => {

  let { posts, value, onChange } = props;

  let options = (!posts) ? [] : posts.map( (post) => ({ 
    label: post.title.rendered, 
    value: post.id,
  }) );

  options.unshift({ label: 'None', value: 0});

  return (
    <SelectControl 
      value={ value }
      options={ options }
      onChange={ onChange }
    />
  );

}); 


// Register Block Type
registerBlockType( json, { 

  edit: (props) => {
    let { attributes, setAttributes, className } = props;
    let classes = classnames( className, 'editor' );

    return (
      <div className={ classes }>
        <ServerSideRender block="nf/form" attributes={ attributes } />

        <InspectorControls>
          <PanelBody title="Form Heading">
            <PlainText 
              value={ attributes.heading } 
              onChange={ ( value ) => setAttributes( { heading: value } ) } 
              placeholder={ 'Type Heading...' } 
            />
          </PanelBody>
          <PanelBody title="Form Chooser">
            <PostChooser 
              value={ attributes.id }
              onChange={ ( value ) => setAttributes({ id: parseInt(value) }) }
            />
          </PanelBody>
        </InspectorControls>
      </div>
    )
  },


  save: (props) => null,

});
