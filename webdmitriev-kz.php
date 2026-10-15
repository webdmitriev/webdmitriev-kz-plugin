<?php
/**
 * Plugin Name: Webdmitriev kz core
 * Plugin URI:  https://webdmitriev.com
 * Description: Кастомный плагин для сайта dreamteammed
 * Version:     1.0.0
 * Author:      Webdmitriev
 * Author URI:  https://webdmitriev.kz
 * License:     GPL v2 or later
 * Text Domain: webdmitriev-kz
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Определение констант плагина.
define( 'WEBDMITRIEV_KZ_VERSION', '1.0.0' );
define( 'WEBDMITRIEV_KZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'WEBDMITRIEV_KZ_URL', plugin_dir_url( __FILE__ ) );

/**
 * Основной класс плагина.
 */
final class Webdmitriev_KZ_Plugin {

  private static $instance = null;

  public static function instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->init_hooks();
    $this->load_modules();
  }

  /**
   * Регистрация базовых хуков.
   */
  private function init_hooks() {
    add_action( 'init', [ $this, 'load_textdomain' ] );
    add_action( 'init', [ $this, 'register_blocks' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
  }

  /**
   * Загрузка локализации.
   */
  public function load_textdomain() {
    load_plugin_textdomain( 'webdmitriev-kz', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
  }

  /**
   * Подключение модулей и компонентов плагина.
   */
  private function load_modules() {
    // Хелперы — всегда, и в админке, и на фронте
    $helpers_file = WEBDMITRIEV_KZ_PATH . 'admin/helpers/helpers.php';
    if ( file_exists( $helpers_file ) ) {
      require_once $helpers_file;
    }

    // Подключение административной панели
    if ( is_admin() ) {
      require_once WEBDMITRIEV_KZ_PATH . 'admin/admin-page.php';
      require_once WEBDMITRIEV_KZ_PATH . 'admin/contacts-page.php';
    }

    // Подключение файла утилизации/управления блоками
    if ( file_exists( WEBDMITRIEV_KZ_PATH . 'admin/register-blocks.php' ) ) {
      require_once WEBDMITRIEV_KZ_PATH . 'admin/register-blocks.php';
    }
  }

  /**
   * Регистрация кастомных блоков Gutenberg.
   */
  public function register_blocks() {
    $blocks_dir = WEBDMITRIEV_KZ_PATH . 'modules/blocks/';

    if ( ! is_dir( $blocks_dir ) ) {
      return;
    }

    // Сканируем папки внутри modules/blocks/
    $block_folders = scandir( $blocks_dir );

    foreach ( $block_folders as $folder ) {
      if ( $folder === '.' || $folder === '..' ) {
        continue;
      }

      $block_path = $blocks_dir . $folder;

      if ( is_dir( $block_path ) && file_exists( $block_path . '/block.json' ) ) {
        register_block_type( $block_path );
      }
    }
  }

  /**
   * Подключение фронтенд-стилей плагина.
   */
  public function enqueue_frontend_assets() {
    $asset_file = WEBDMITRIEV_KZ_PATH . 'build/frontend.asset.php';

    if ( ! file_exists( $asset_file ) ) {
      return;
    }

    $asset = require $asset_file;

    wp_enqueue_style(
      'webdmitriev-kz-frontend',
      WEBDMITRIEV_KZ_URL . 'build/frontend.css',
      [],
      $asset['version'] ?? WEBDMITRIEV_KZ_VERSION
    );
  }
}

/**
 * Запуск плагина.
 */
function webdmitriev_kz_run() {
  return Webdmitriev_KZ_Plugin::instance();
}

webdmitriev_kz_run();