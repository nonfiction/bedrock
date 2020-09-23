import json from './block.json';
import classnames from 'classnames';

const { registerBlockType } = nf;
const { serverSideRender: ServerSideRender } = wp;
const { registerBlockStyle } = wp.blocks;
const { compose, withState } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { Button, TextControl, PanelBody, SelectControl, Toolbar, } = wp.components;
const { BlockControls, BlockIcon, ColorPalette, InnerBlocks, InspectorControls, MediaPlaceholder, MediaReplaceFlow, MediaUpload, MediaUploadCheck, PlainText, RichText, withColors, URLInputButton } = wp.blockEditor;


registerBlockType( json, { 

  edit: (props) => {
    let { attributes, setAttributes, className } = props;
    let classes = classnames( className, 'editor', 'nf-statistic' );

    return (
      <div className={ classes }>

        <div className="nf-statistic__number">

          <RichText tagName="span" className="number"
            value={ attributes.number }
            onChange={ ( value ) => setAttributes( { number: value } ) }
            placeholder={ '123' }
            formattingControls={ [] }
            keepPlaceholderOnFocus
          />

          <RichText tagName="span" className="extra"
            value={ attributes.extra }
            onChange={ ( value ) => setAttributes( { extra: value } ) }
            placeholder={ '_' }
            formattingControls={ [] }
            keepPlaceholderOnFocus
          />
        </div>

        <RichText tagName="div" className="nf-statistic__label"
          value={ attributes.label }
          onChange={ ( value ) => setAttributes( { label: value } ) }
          placeholder={ 'Label...' }
          formattingControls={ [] }
          keepPlaceholderOnFocus
        />

        <BlockControls>
          <URLInputButton
            url={ attributes.link }
            onChange={ ( value ) => setAttributes( { link: value } ) }
          />
        </BlockControls>

      </div>
    )
  },


  save: (props) => null,

});
