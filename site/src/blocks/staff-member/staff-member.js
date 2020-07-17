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


registerBlockType( 'nf/staff-member', { 

  edit: (props) => {
    let { attributes, setAttributes, className } = props;
    let classes = classnames( className, 'editor' );
    
    let img = '/app/site/src/assets/img';

    let style =  { 
      backgroundImage: `url(${attributes.background_url})`,
    };

    // alert(__dirname);
              
    return (
      <div className={ classes }>

      <div class="staff-member" style={ style }> 

        <figure class="staff-member__figure">
          <img class="staff-member__photo" src={ attributes.photo_url } />

          <RichText
            tagName="h3" className="staff-member__name"
            value={ attributes.name }
            onChange={ ( value ) => setAttributes( { name: value } ) }
            placeholder={ 'Type name...' }
            keepPlaceholderOnFocus={true}
          />

          <RichText
            tagName="h4" className="staff-member__title"
            value={ attributes.title }
            onChange={ ( value ) => setAttributes( { title: value } ) }
            placeholder={ 'Type title...' }
            keepPlaceholderOnFocus={true}
          />

          <div class="staff-member__link">
            <PlainText
              value={ attributes.linkedin }
              onChange={ ( value ) => setAttributes( { linkedin: value } ) }
              placeholder={ 'Type LinkedIn URL...' }
              keepPlaceholderOnFocus={true}
            />
          </div>

          <div class="staff-member__link">
            <PlainText
              value={ attributes.email }
              onChange={ ( value ) => setAttributes( { email: value } ) }
              placeholder={ 'Type email...' }
              keepPlaceholderOnFocus={true}
            />
          </div>

        </figure>


          <RichText
            tagName="div" className="staff-member__content"
            value={ attributes.content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
            placeholder={ 'Type something...' }
            keepPlaceholderOnFocus={true}
          />
      </div>


        {/* <ServerSideRender block="nf/staff-member" attributes={ attributes } /> */}


        <BlockControls>

          <MediaUploadCheck>
            <MediaUpload
              allowedTypes={ ['image'] }

              onSelect={ ( media ) => setAttributes({ photo_url: media.url, photo_id: media.id }) }
              value={ attributes.photo_id }
              
              render={ ( { open } ) => (
                <div className="components-toolbar">
                  <Button onClick={ open } icon="format-image" label="Photo" />
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
