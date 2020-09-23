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
    let { attributes, setAttributes, className } = props;
    let classes = classnames( className, 'editor', 'nf-location' );
    
    return (
      <div className={ classes }>

        <RichText
          tagName="div" className="nf-location__name"
          value={ attributes.name }
          onChange={ ( value ) => setAttributes( { name: value } ) }
          placeholder={ '...' }
        />
        <RichText
          tagName="div" className="nf-location__office"
          value={ attributes.office }
          onChange={ ( value ) => setAttributes( { office: value } ) }
          placeholder={ 'Type Something...' }
        />
        <RichText
          tagName="div" className="nf-location__address"
          value={ attributes.address }
          onChange={ ( value ) => setAttributes( { address: value } ) }
          placeholder={ 'Type an mailing address in this space...' }
        />
        <div className="nf-location__phone">
          Telephone: <RichText
            tagName="a" href 
            value={ attributes.phone }
            onChange={ ( value ) => setAttributes( { phone: value } ) }
            placeholder={ '555.555.5555' }
          />
        </div>
        <div className="nf-location__email">
          Email: <RichText
            tagName="a" href 
            value={ attributes.email }
            onChange={ ( value ) => setAttributes( { email: value } ) }
            placeholder={ 'info@email.com' }
          />
        </div>
      </div>
    )
  },


  save: (props) => null,

});
