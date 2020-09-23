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
    let classes = classnames( className, 'editor', 'nf-icon-text' );

    return (
      <div className={ classes }>

        <figure className="nf-icon-text__figure">
          <img rel={ attributes.icon_id } src={ attributes.icon_url } />
        </figure>

        <RichText tagName="div" className="nf-icon-text__heading"
          value={ attributes.heading }
          onChange={ ( value ) => setAttributes( { heading: value } ) }
          placeholder={ 'Type Heading...' }
        />

        <RichText tagName="div" className="nf-icon-text__content"
          value={ attributes.content }
          onChange={ ( value ) => setAttributes( { content: value } ) }
          placeholder={ 'Type Content...' }
        />

        <BlockControls>
          <MediaUploadCheck>
            <MediaUpload
              allowedTypes={ ['image'] }
              onSelect={ ( media ) => setAttributes({ icon_url: media.url, icon_id: media.id }) }
              value={ attributes.icon_id }
              render={ ({ open }) => <div className="components-toolbar"><Button onClick={ open } icon="format-image" label="Photo" /></div> }
            />
          </MediaUploadCheck>
        </BlockControls>

      </div>
    )
  },


  save: (props) => null,

});
