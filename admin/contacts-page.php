<?php
/**
 * Модуль управления контактами в админке.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Webdmitriev_KZ_Contacts_Admin {

  private $option_name = 'webdmitriev_kz_contacts';

  public function __construct() {
    add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
    add_action( 'admin_init', [ $this, 'register_settings' ] );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
  }

  public function add_menu_page() {
    add_submenu_page(
      'webdmitriev-kz',
      __( 'Управление контактами', 'webdmitriev-kz' ),
      __( 'Контакты', 'webdmitriev-kz' ),
      'manage_options',
      'webdmitriev-kz-contacts',
      [ $this, 'render_page' ]
    );
  }

  /**
   * Подключение CSS и JS страниц контактов в админке.
   */
  public function enqueue_admin_assets( $hook ) {
    if ( strpos( $hook, 'webdmitriev-kz-contacts' ) === false ) {
      return;
    }

    wp_enqueue_style(
      'webdmitriev-kz-admin-contacts',
      WEBDMITRIEV_KZ_URL . 'admin/css/admin-contacts.css',
      [],
      WEBDMITRIEV_KZ_VERSION
    );

    wp_enqueue_script(
      'webdmitriev-kz-admin-contacts',
      WEBDMITRIEV_KZ_URL . 'admin/js/admin-contacts.js',
      [],
      WEBDMITRIEV_KZ_VERSION,
      true
    );
  }

  public function register_settings() {
    register_setting(
      'webdmitriev_kz_contacts_group',
      $this->option_name,
      [
        'type'              => 'array',
        'sanitize_callback' => [ $this, 'sanitize_contacts' ],
        'default'           => [],
      ]
    );
  }

  /**
   * Генерация уникального ID: первые 4 буквы названия + 6 цифр.
   */
  public static function generate_city_id( $name = '' ) {
    $cyrillic = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'];
    $latin    = ['a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','shch','','y','','e','yu','ya'];
    
    $name_lat = str_replace( $cyrillic, $latin, mb_strtolower( $name ) );
    $clean_name = preg_replace( '/[^a-z]/', '', $name_lat );
    
    if ( empty( $clean_name ) ) {
      $clean_name = 'city';
    }
    
    $prefix = substr( $clean_name, 0, 4 );
    $prefix = str_pad( $prefix, 4, 'x' );
    
    $digits = mt_rand( 100000, 999999 );
    
    return $prefix . $digits;
  }

  /**
   * Санитайзер для массива городов и их полей.
   */
  public function sanitize_contacts( $input ) {
    $sanitized = [];

    if ( ! is_array( $input ) ) {
      return $sanitized;
    }

    foreach ( $input as $city ) {
      if ( empty( $city['name'] ) && empty( $city['address'] ) ) {
        continue; // Пропускаем пустые записи
      }

      // Сохраняем существующий ID или генерируем новый, если его нет
      $city_id = sanitize_text_field( $city['id'] ?? '' );
      if ( empty( $city_id ) ) {
        $city_id = self::generate_city_id( $city['name'] ?? '' );
      }

      $sanitized[] = [
        'id'            => $city_id,
        'name'          => sanitize_text_field( $city['name'] ?? '' ),
        'name_kz'       => sanitize_text_field( $city['name_kz'] ?? '' ),
        'phone1'        => sanitize_text_field( $city['phone1'] ?? '' ),
        'phone2'        => sanitize_text_field( $city['phone2'] ?? '' ),
        'email1'        => sanitize_email( $city['email1'] ?? '' ),
        'email2'        => sanitize_email( $city['email2'] ?? '' ),
        'address'       => sanitize_text_field( $city['address'] ?? '' ),
        'address_kz'    => sanitize_text_field( $city['address_kz'] ?? '' ),
        'time1'         => sanitize_text_field( $city['time1'] ?? '' ),
        'time1_kz'      => sanitize_text_field( $city['time1_kz'] ?? '' ),
        'time2'         => sanitize_text_field( $city['time2'] ?? '' ),
        'time2_kz'      => sanitize_text_field( $city['time2_kz'] ?? '' ),
        'socialTg'      => sanitize_text_field( $city['socialTg'] ?? '' ),
        'socialVk'      => sanitize_text_field( $city['socialVk'] ?? '' ),
        'socialMax'     => sanitize_text_field( $city['socialMax'] ?? '' ),
        'socialOk'      => sanitize_text_field( $city['socialOk'] ?? '' ),
        'socialInst'    => sanitize_text_field( $city['socialInst'] ?? '' ),
        'socialTikTok'  => sanitize_text_field( $city['socialTikTok'] ?? '' ),
        'map_iframe'    => wp_kses( $city['map_iframe'] ?? '', [
          'iframe' => [
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'style'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'referrerpolicy'  => true,
          ],
        ] ),
        'map_iframe_kz' => wp_kses( $city['map_iframe_kz'] ?? '', [
          'iframe' => [
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'style'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'referrerpolicy'  => true,
          ],
        ] ),
      ];
    }

    return $sanitized;
  }

  /**
   * Форматирование телефона для ссылки tel:
   */
  public static function format_phone( $phone ) {
    return preg_replace( '/[^0-9+]/', '', $phone );
  }

  public function render_page() {
    $contacts = get_option( $this->option_name, [] );
    ?>
    <div class="wrap">
      <h1><?php _e( 'Управление контактами по городам', 'webdmitriev-kz' ); ?></h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'webdmitriev_kz_contacts_group' ); ?>
        <div id="contacts-repeatable-wrapper">
          <?php if ( ! empty( $contacts ) ) : ?>
            <?php foreach ( $contacts as $index => $city ) : ?>
              <?php $this->render_city_row( $index, $city ); ?>
            <?php endforeach; ?>
          <?php else : ?>
            <?php $this->render_city_row( 0, [] ); ?>
          <?php endif; ?>
        </div>

        <p>
          <button type="button" class="button button-secondary" id="add-city-btn">
            <?php _e( '+ Добавить город', 'webdmitriev-kz' ); ?>
          </button>
        </p>

        <?php submit_button( __( 'Сохранить контакты', 'webdmitriev-kz' ) ); ?>
      </form>
    </div>
    <?php
  }

  private function render_city_row( $index, $data ) {
    $city_id = $data['id'] ?? '';
    if ( empty( $city_id ) ) {
      $city_id = self::generate_city_id( $data['name'] ?? '' );
    }
    ?>
    <div class="city-row">
      <button type="button" class="button remove-city-btn"></button>

      <!-- Скрытое поле ID и визуальная подсказка -->
      <input type="hidden" name="webdmitriev_kz_contacts[<?php echo $index; ?>][id]" value="<?php echo esc_attr( $city_id ); ?>">
      <div style="margin-bottom: 10px; font-size: 12px; color: #666;">
        <?php _e( 'ID города:', 'webdmitriev-kz' ); ?> <code><?php echo esc_html( $city_id ); ?></code>
      </div>

      <div class="city-row-group">
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.3);">
          <label><strong><?php _e( 'Название города:', 'webdmitriev-kz' ); ?></strong></label><br>
          <input type="text" class="large-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][name]" value="<?php echo esc_attr( $data['name'] ?? '' ); ?>" required>
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.8);">
          <label><strong><?php _e( 'Название города KZ:', 'webdmitriev-kz' ); ?></strong></label><br>
          <input type="text" class="large-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][name_kz]" value="<?php echo esc_attr( $data['name_kz'] ?? '' ); ?>" required>
        </p>
      </div>
      <div class="city-row-group">
        <p>
          <label><?php _e( 'Телефон 1:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][phone1]" value="<?php echo esc_attr( $data['phone1'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Телефон 2:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][phone2]" value="<?php echo esc_attr( $data['phone2'] ?? '' ); ?>">
        </p>
      </div>
      <div class="city-row-group">
        <p>
          <label><?php _e( 'Email 1:', 'webdmitriev-kz' ); ?></label><br>
          <input type="email" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][email1]" value="<?php echo esc_attr( $data['email1'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Email 2:', 'webdmitriev-kz' ); ?></label><br>
          <input type="email" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][email2]" value="<?php echo esc_attr( $data['email2'] ?? '' ); ?>">
        </p>
      </div>
      <div class="city-row-group">
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.3);">
          <label><?php _e( 'Время работы 1:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][time1]" value="<?php echo esc_attr( $data['time1'] ?? '' ); ?>">
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.8);">
          <label><?php _e( 'Время работы 1 KZ:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][time1_kz]" value="<?php echo esc_attr( $data['time1_kz'] ?? '' ); ?>">
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.3);">
          <label><?php _e( 'Время работы 2:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][time2]" value="<?php echo esc_attr( $data['time2'] ?? '' ); ?>">
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.8);">
          <label><?php _e( 'Время работы 2 KZ:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][time2_kz]" value="<?php echo esc_attr( $data['time2_kz'] ?? '' ); ?>">
        </p>
      </div>
      <div class="city-row-group">
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.3);">
          <label><?php _e( 'Адрес:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="large-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][address]" value="<?php echo esc_attr( $data['address'] ?? '' ); ?>">
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.8);">
          <label><?php _e( 'Адрес KZ:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="large-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][address_kz]" value="<?php echo esc_attr( $data['address_kz'] ?? '' ); ?>">
        </p>
      </div>
      <div class="city-row-group">
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.3);">
          <label><?php _e( 'iframe карты:', 'webdmitriev-kz' ); ?></label><br>
          <textarea class="large-text" rows="10" name="webdmitriev_kz_contacts[<?php echo $index; ?>][map_iframe]"><?php echo esc_textarea( $data['map_iframe'] ?? '' ); ?></textarea>
        </p>
        <p style="padding: 4px;background-color: rgba(178, 180, 205, 0.8);">
          <label><?php _e( 'iframe карты KZ:', 'webdmitriev-kz' ); ?></label><br>
          <textarea class="large-text" rows="10" name="webdmitriev_kz_contacts[<?php echo $index; ?>][map_iframe_kz]"><?php echo esc_textarea( $data['map_iframe_kz'] ?? '' ); ?></textarea>
        </p>
      </div>
      <div class="city-row-3-group">
        <p>
          <label><?php _e( 'Telegram:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialTg]" value="<?php echo esc_attr( $data['socialTg'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Vk:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialVk]" value="<?php echo esc_attr( $data['socialVk'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Max:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialMax]" value="<?php echo esc_attr( $data['socialMax'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Ok:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialOk]" value="<?php echo esc_attr( $data['socialOk'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'Inst:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialInst]" value="<?php echo esc_attr( $data['socialInst'] ?? '' ); ?>">
        </p>
        <p>
          <label><?php _e( 'TikTok:', 'webdmitriev-kz' ); ?></label><br>
          <input type="text" class="regular-text" name="webdmitriev_kz_contacts[<?php echo $index; ?>][socialTikTok]" value="<?php echo esc_attr( $data['socialTikTok'] ?? '' ); ?>">
        </p>
      </div>
    </div>
    <?php
  }
}

new Webdmitriev_KZ_Contacts_Admin();