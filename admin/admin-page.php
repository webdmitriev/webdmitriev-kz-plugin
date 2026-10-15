<?php
/**
 * Admin Page Module
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Webdmitriev_KZ_Admin {

  public function __construct() {
    add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
  }

  /**
   * Добавление пункта меню в админку WordPress.
   */
  public function add_plugin_admin_menu() {
    add_menu_page(
      __( 'Настройки Webdmitriev', 'webdmitriev-kz' ),
      __( 'Webdmitriev kz', 'webdmitriev-kz' ),
      'manage_options',
      'webdmitriev-kz',
      [ $this, 'display_admin_page' ],
      'dashicons-admin-generic',
      30
    );
  }

  /**
   * HTML-содержимое страницы настроек.
   */
  public function display_admin_page() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <p><?php _e( 'Добро пожаловать в панель управления плагином', 'webdmitriev-kz' ); ?></p>

      <div id="webdmitriev-admin-app">
        <!-- Сюда в будущем можно вынести интерфейс на React/Vue или стандартные формы -->
      </div>
    </div>
    <?php
  }
}

// Инициализация админ-панели
new Webdmitriev_KZ_Admin();