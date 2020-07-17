import classnames from 'classnames';

const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;

registerBlockType( 'nf/example-static', {

  title: 'Example Static',
  icon: 'image-filter',
  category: 'common',

  supports: {
    align: true,
    html: true,
  },

  attributes: {
    align: {
      type: 'string',
    },
    content: {
      type: 'array',
      source: 'children',
      selector: '.words',
    },
    title: {
      type: 'string',
      source: 'text',
      selector: 'h2' ,
      default: 'Hello World!',
    },
  },

  example: {
    attributes: {
      title: 'Greetings Earth',
    },
  },

  edit: (props) => {
    const { className, attributes, setAttributes } = props;
    const classes = classnames(className);

    return (
      <div className={ classes }>
        <h2>{ attributes.title }</h2>
        <RichText
          tagName="div"
          className="words"
          value={ attributes.content }
          onChange={ ( newContent ) => setAttributes( { content: newContent } ) }
          placeholder={ 'Enter text here' }
          keepPlaceholderOnFocus={true}
        />
        <hr />
      </div>
    );
  },

  save: (props) => {
    const { className, attributes } = props;
    const classes = classnames(className);
    return (
      <div className={ classes }>
        <h2>{ attributes.title }</h2>
        <div className="words">
          { attributes.content }
        </div>
        <hr />
      </div>
    );
  },

} );
