import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';

registerBlockType( 'webdmitriev/simple-box', {
  edit: ( { attributes, setAttributes } ) => {
    const blockProps = useBlockProps( {
      style: { border: '2px dashed #007cba', padding: '20px', borderRadius: '8px' }
    } );

    return (
      <div { ...blockProps }>
        <p><strong>Простой блок-бокс (Редактор):</strong></p>
        <RichText
          tagName="div"
          placeholder="Введите текст..."
          value={ attributes.content }
          onChange={ ( content ) => setAttributes( { content } ) }
        />
      </div>
    );
  },
  save: ( { attributes } ) => {
    const blockProps = useBlockProps.save( {
      style: { border: '2px solid #333', padding: '20px', borderRadius: '8px', backgroundColor: '#f9f9f9' }
    } );

    return (
      <div { ...blockProps }>
        <RichText.Content tagName="div" value={ attributes.content } />
      </div>
    );
  },
} );