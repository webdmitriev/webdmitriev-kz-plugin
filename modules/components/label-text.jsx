/**
 * Переиспользуемый компонент: Лейбл + RichText блок
  import LabelText from '../../components/label-text';
  <LabelText
    label={__('Заголовок', 'theme')}
    value={title}
    onChange={(newTitle) => setAttributes({ title: newTitle })}
  />
 */
import { __ } from '@wordpress/i18n';
import { Flex, FlexBlock } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';

export default function LabelText({
  label,
  value,
  onChange,
  tagName = 'div',
  placeholder = __('Текст...', 'theme'),
  allowedFormats = undefined, // По умолчанию разрешены все, или передайте [] если нужно запретить
  marginBottom = 12,
}) {
  return (
    <Flex style={{ marginBottom }}>
      <FlexBlock style={{ padding: 8, border: '1px dashed rgba(0,124,186,.5)', borderRadius: 4 }}>
        {label && <label className="km-label">{label}</label>}
        <RichText
          tagName={tagName}
          value={value}
          onChange={onChange}
          placeholder={placeholder}
          allowedFormats={allowedFormats}
          style={{ padding: 8, border: '1px dashed rgba(0,124,186,.8)', borderRadius: 4 }}
        />
      </FlexBlock>
    </Flex>
  );
}