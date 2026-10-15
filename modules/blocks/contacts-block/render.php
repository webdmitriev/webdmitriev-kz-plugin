<?php
/**
 * Динамический шаблон рендеринга блока контактов
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// attributes
$title    = $attributes['title']; // String
$title_kz = $attributes['title_kz']; // String
$kz       = $attributes['kz']; // RU / KZ

// Запрашиваем данные из базы
$contacts = get_option( 'webdmitriev_kz_contacts', [] );
if ( empty( $contacts ) || ! is_array( $contacts ) ) {
  return;
}

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'webdmitriev-contacts-block' ] );

$contacts_json = [];

foreach ( $contacts as $city ) {
  if ( empty( $city['name'] ) ) {
    continue;
  }
  $contacts_json[] = [
    'id'            => $city['id'] ?? '',
    'name'          => $city['name'] ?? '',
    'nam_kz'        => $city['name_kz'] ?? '',
    'phone1'        => $city['phone1'] ?? '',
    'phone2'        => $city['phone2'] ?? '',
    'email1'        => $city['email1'] ?? '',
    'email2'        => $city['email2'] ?? '',
    'address'       => $city['address'] ?? '',
    'address_kz'    => $city['address_kz'] ?? '',
    'time1'         => $city['time1'] ?? '',
    'time1_kz'      => $city['time1_kz'] ?? '',
    'time2'         => $city['time2'] ?? '',
    'time2_kz'      => $city['time2_kz'] ?? '',
    'socialTg'      => $city['socialTg'] ?? '',
    'socialVk'      => $city['socialVk'] ?? '',
    'socialMax'     => $city['socialMax'] ?? '',
    'socialOk'      => $city['socialOk'] ?? '',
    'socialInst'    => $city['socialInst'] ?? '',
    'socialTikTok'  => $city['socialTikTok'] ?? '',
    'map_iframe'    => $city['map_iframe'] ?? '',
    'map_iframe_kz' => $city['map_iframe_kz'] ?? '',
  ];
}
wp_localize_script( 'contacts-script', 'contactsData', $contacts_json );
?>

<div class="wp-block-theme-block-11 block-standard block-11 block-11-kz">
  <div class="container df-fs-fs">
    <?php if(webdmitriev_is_lang( $kz, 'RU' ) && $title): ?><h2 class="h2"><?php echo esc_html($title); ?></h2><?php endif; ?>
    <?php if(webdmitriev_is_lang( $kz, 'KZ' ) && $title_kz): ?><h2 class="h2"><?php echo esc_html($title_kz); ?></h2><?php endif; ?>
    <div class="contacts-items">
      <div class="contacts-item contacts-item-map" style="border-radius: 0;">
        <div class="contacts-item__tabs">
          <?php $first = true; foreach ( $contacts as $city ) : ?>
            <?php if($city['name'] || $city['name_kz']): ?>
              <div class="contacts-item__tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_html( $city['id'] ); ?>">
                <?php echo webdmitriev_is_lang( $kz, 'RU' ) ? esc_html( $city['name'] ) : esc_html( $city['name_kz'] ); ?>
              </div>
            <?php $first = false; endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="contacts-item contacts-item-data df-fs-fs">
        <div class="contacts-data">
          <!--  -->
        </div>
        <div class="contacts-socials df-fs-ce">
          <!--  -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    window.contactsData = <?php echo wp_json_encode( $contacts_json ); ?>;
    window.CONTACTS_LANG = {
      isRu: <?php echo webdmitriev_is_lang($kz, 'RU') ? 'true' : 'false'; ?>
    };

    if (!window.contactsData || window.contactsData.length === 0) {
      return;
    }

    const contactsItemMap  = document.querySelector('.contacts-item.contacts-item-map');
    const contactsItemData = document.querySelector('.contacts-item.contacts-item-data');
    const contactsItemTabs = document.querySelectorAll('.contacts-item__tab');

    // Навешиваем обработчики
    contactsItemTabs?.forEach(tab => {
      tab?.addEventListener('click', () => {
        activateTab(tab);
      });
    });

    // Активируем первый таб при загрузке
    beginning();

    function beginning() {
      const firstTab = contactsItemTabs?.[0];
      if (!firstTab) return;
      activateTab(firstTab);
    }

    // Вся логика активации таба в одном месте
    function activateTab(tab) {
      const tabId = tab.getAttribute('data-tab');

      changedActiveTab(tab);
      cleanData();

      window.contactsData.forEach(data => {
        if (data.id === tabId) {
          addMap(data);
          addContacts(data);
          addSocials(data)
        }
      });
    }

    function changedActiveTab(tab) {
      contactsItemTabs.forEach(el => el.classList.remove('active'));
      tab.classList.add('active');
    }

    function addMap(data) {
      contactsItemMap.insertAdjacentHTML('beforeend', data.map_iframe);
    }

    function addContacts(data) {
      if (!data || typeof data !== 'object') return;

      const isRu = window.CONTACTS_LANG?.isRu ?? true;

      const address   = isRu ? data.address : data.address_kz;
      const workTime1  = isRu ? data.time1 : data.time1_kz;
      const workTime2  = isRu ? data.time2 : data.time2_kz;
      const phone1    = data.phone1;
      const phone2    = data.phone2;
      const email1    = data.email1;
      const email2    = data.email2;

      const phoneHref1 = keepDigitsAndPlus(phone1);
      const phoneHref2 = keepDigitsAndPlus(phone2);
      const emailHref1 = email1 ? String(email1).trim() : '';
      const emailHref2 = email2 ? String(email2).trim() : '';

      const html = [
        buildParagraph({
          value: address,
          className: 'icon-map-accent',
        }),
        phoneHref1 && buildParagraph({
          value: phone1,
          className: 'icon-phone-accent',
          linkHref: `tel:${phoneHref1}`,
        }),
        phoneHref2 && buildParagraph({
          value: phone2,
          className: 'icon-phone-accent',
          linkHref: `tel:${phoneHref2}`,
        }),
        emailHref1 && buildParagraph({
          value: email1,
          className: 'icon-mail-accent',
          linkHref: `mailto:${emailHref1}`,
        }),
        emailHref2 && buildParagraph({
          value: email2,
          className: 'icon-mail-accent',
          linkHref: `mailto:${emailHref2}`,
        }),
        buildParagraph({
          value: workTime1,
          className: 'icon-time-accent',
        }),
        buildParagraph({
          value: workTime2,
          className: 'icon-time-accent',
        }),
      ].filter(Boolean).join('');

      if (!html) return;

      const target = contactsItemData.querySelector('.contacts-data');
      if (!target) return;

      target.insertAdjacentHTML('beforeend', html);
    }

    function addSocials(data) {
      const container = contactsItemData.querySelector('.contacts-socials');

      const socials = [
        { url: data.socialTg, cls: 'icon-telegram-accent', label: 'telegram' },
        { url: data.socialVk, cls: 'icon-vk-accent',       label: 'vk' },
        { url: data.socialMax, cls: 'icon-maks-accent', label: 'max' },
        { url: data.socialOk, cls: 'icon-ok-accent', label: 'ok' },
        { url: data.socialInst, cls: 'icon-inst-accent', label: 'inst' },
        { url: data.socialTikTok, cls: 'icon-tiktok-accent', label: 'tiktok' },
      ];

      const html = socials
        .filter(s => s.url && s.url.trim() !== '')
        .map(s => `<a href="${s.url}" class="icon-social ${s.cls}" target="_blank" rel="noopener noreferrer" aria-label="${s.label}"></a>`)
        .join('');

      if (html) container.insertAdjacentHTML('beforeend', html);
    }

    function cleanData() {
      contactsItemMap.querySelector('iframe')?.remove();
      contactsItemData.querySelector('.contacts-data').innerHTML = "";
      contactsItemData.querySelector('.contacts-socials').innerHTML = "";
    }

    // HELPERS
    function keepDigitsAndPlus(str) {
      return str.replace(/[^0-9+]/g, '');
    }

    // Утилита: экранирование HTML для защиты от XSS и поломки разметки
    function escapeHtml(str) {
      if (str == null) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    // Утилита: оставить только цифры и "+" в начале
    function keepDigitsAndPlus(value) {
      if (value == null) return '';
      const str = String(value);
      const hasPlus = str.trim().startsWith('+');
      const digits = str.replace(/\D/g, '');
      return hasPlus ? `+${digits}` : digits;
    }

    // Утилита: собрать <p>, только если значение непустое
    function buildParagraph({ value, className, linkHref, linkText }) {
      if (value == null || String(value).trim() === '') return '';

      const text = escapeHtml(value);
      const inner = linkHref
        ? `<a href="${escapeHtml(linkHref)}">${escapeHtml(linkText ?? value)}</a>`
        : text;

      return `<p class="descr ${className}">${inner}</p>`;
    }
  });
</script>