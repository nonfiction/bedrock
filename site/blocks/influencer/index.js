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

    attributes.width = attributes.width || 'wide';
    attributes.valign = attributes.valign || 'middle';
    attributes.halign = attributes.halign || 'left';

    let classes = classnames( className, 'editor', 'nf-influencer', `is-${attributes.width}`, `is-${attributes.valign}`, `is-${attributes.halign}` );

    return (
      <div className={ classes }>
        <figure className="nf-influencer__figure">
          <img className="nf-influencer__photo" src={ attributes.photo_url } />
        </figure>
        <div className="nf-influencer__details">
          <div className="nf-influencer__text">
            <RichText tagName="h3" className="nf-influencer__name"
              value={ attributes.name }
              onChange={ ( value ) => setAttributes( { name: value } ) }
              placeholder={ 'Type Name...' }
            />
            <RichText tagName="h4" className="nf-influencer__role"
              value={ attributes.role }
              onChange={ ( value ) => setAttributes( { role: value } ) }
              placeholder={ 'Type Role...' }
            />
            <RichText tagName="div" className="nf-influencer__content"
              value={ attributes.content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
              placeholder={ 'Type Content...' }
            />
          </div>
        </div>

        {/* <ServerSideRender block="nf/influencer" attributes={ attributes } /> */}

        <BlockControls>
          <MediaUploadCheck>
            <MediaUpload
              allowedTypes={ ['image'] }
              onSelect={ ( media ) => setAttributes({ photo_url: media.url, photo_id: media.id }) }
              value={ attributes.photo_id }
              render={ ({ open }) => <div className="components-toolbar"><Button onClick={ open } icon="format-image" label="Photo" /></div> }
            />
          </MediaUploadCheck>
        </BlockControls>

        <InspectorControls>
          <PanelBody title="Layout Settings">
            <SelectControl
              label='Width'
              value={ attributes.width }
              options={[ {label:'Narrow',value:'narrow'}, {label:'Wide',value:'wide'} ]}
              onChange={ ( width ) => { props.setAttributes( { width } ); } }
            />
            <SelectControl
              label='Vertical Alignment'
              value={ attributes.valign }
              options={[ {label:'Top',value:'top'}, {label:'Middle',value:'middle'}, {label:'Bottom',value:'bottom'} ]}
              onChange={ ( valign ) => { props.setAttributes( { valign } ); } }
            />
            <SelectControl
              label='Horizontal Alignment'
              value={ attributes.halign }
              options={[ {label:'Left',value:'left'}, {label:'Right',value:'right'} ]}
              onChange={ ( halign ) => { props.setAttributes( { halign } ); } }
            />
          </PanelBody>
        </InspectorControls>

      </div>
    )
  },


  save: (props) => null,

});
