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
    let classes = classnames( className, 'editor' );
    
    let style =  { 
      backgroundImage: `url(${attributes.background_url})`,
    };
              
    return (
      <div className={ classes }>

        <div className="nf-banner" style={ style }>
          <div className="nf-banner__inner">
            <RichText tagName="h1" className="nf-banner__heading" 
              placeholder={ 'Enter text here' } 
              value={ attributes.heading }
              onChange={ ( value ) => setAttributes( { heading: value } ) }
            />
            <RichText tagName="h2"  className="nf-banner__content"
              placeholder={ 'Enter content here' } 
              value={ attributes.content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
            />
          </div>
        </div>

        {/* <ServerSideRender block="nf/banner" attributes={ attributes } /> */}


        <BlockControls>

          <MediaUploadCheck>
            <MediaUpload
              allowedTypes={ ['image'] }

              onSelect={ ( media ) => setAttributes({ background_url: media.url, background_id: media.id }) }
              value={ attributes.background_id }
              
              render={ ( { open } ) => (
                <div className="components-toolbar">
                  <Button onClick={ open } icon="format-image" label="Background Image" />
                </div>
              ) }
            />
          </MediaUploadCheck>

        </BlockControls>


        <InspectorControls>
          <PanelBody title="Video Chooser">
            <MediaUploadCheck>
              <MediaUpload
                allowedTypes={ ['video'] }

                onSelect={ ( media ) => setAttributes({ video_url: media.url, video_id: media.id }) }
                value={ attributes.video_id }
                
                render={ ( { open } ) => (
                  <Button onClick={ open } icon="format-image" label="Video" />
                ) }
              />
            </MediaUploadCheck>
          </PanelBody>
        </InspectorControls>

      </div>
    )
  },


  save: (props) => null,

});

