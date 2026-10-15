import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Flex, FlexBlock, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import LabelText from '../../components/label-text';

export default function Edit( { attributes, setAttributes } ) {
  const { title, title_kz, kz } = attributes;
  const blockProps = useBlockProps( { className: 'data-block wd-contacts-editor' } );

  return (
    <>
      <InspectorControls>
        <PanelBody title={ __( 'Настройки контактов', 'webdmitriev-kz' ) }>
          <Flex>
            <FlexBlock>
              <SelectControl
                value={kz}
                options={[
                  { label: 'RU', value: 'RU' },
                  { label: 'KZ', value: 'KZ' },
                ]}
                onChange={(value) => setAttributes({ kz: value })}
              />
            </FlexBlock>
          </Flex>
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <div className="wd-contacts-editor__title">📍 Блок контактов</div>
        <div className="wd-contacts-editor__state">
          <LabelText
            label={__('Заголовок', 'theme')}
            value={title}
            onChange={(newTitle) => setAttributes({ title: newTitle })}
          />
          <LabelText
            label={__('Заголовок KZ', 'theme')}
            value={title_kz}
            onChange={(newTitle) => setAttributes({ title_kz: newTitle })}
          />
          { kz == "KZ" ? 'Включён режим: Kz' : 'Включён режим: Russia' }
        </div>
      </div>
    </>
  );
}