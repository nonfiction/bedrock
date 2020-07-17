import classnames from 'classnames';

const { registerBlockType } = wp.blocks;
const { RichText, PlainText } = wp.blockEditor;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType( 'nf/example-dynamic', {

  title: 'Example Dynamic',
  icon: 'money',
  category: 'common',

  supports: {
    align: true,
    html: false,
  },

  // These attributes must match index.php
  attributes: {
    align: {
      type: 'string',
    },
    content: {
      type: 'string',
    },
  },

  example: {
    attributes: {
      content: 'Hello World',
    },
  },

  edit: (props) => {
    const { className, attributes, setAttributes } = props;
    const classes = classnames(className, 'editor');

    return (
      <div className={ classes }>
        <ServerSideRender block="nf/example-dynamic" attributes={ attributes } />
        <RichText
          tagName="div"
          className="richtext"
          value={ attributes.greeting }
          onChange={ ( value ) => setAttributes( { content: value } ) }
          placeholder={ 'Type something...' }
          keepPlaceholderOnFocus={true}
        />
      </div>
    );
  },

  save: (props) => null,

} );
