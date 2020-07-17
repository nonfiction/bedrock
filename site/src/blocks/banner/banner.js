import block from './block.json';
import classnames from 'classnames';

const { serverSideRender: ServerSideRender } = wp;
const { registerBlockType } = wp.blocks;

const {
  Button,
  TextControl,
} = wp.components;

const {
  BlockControls,
  BlockIcon,
  ColorPalette,
  InnerBlocks,
  InspectorControls,
  MediaPlaceholder,
  MediaReplaceFlow,
  MediaUpload,
  MediaUploadCheck,
  PlainText,
  RichText,
  withColors,
} = wp.blockEditor;


registerBlockType( 'nf/banner', { 

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
              placeholder={ 'Enter text here' } keepPlaceholderOnFocus={true}
              value={ attributes.heading }
              onChange={ ( value ) => setAttributes( { heading: value } ) }
            />
            <RichText tagName="h2"  className="nf-banner__content"
              placeholder={ 'Enter content here' } keepPlaceholderOnFocus={true}
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


      </div>
    )
  },


  save: (props) => null,

...block });
