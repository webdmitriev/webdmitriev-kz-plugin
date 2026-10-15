<?php
/**
 * Управление блоками и категориями Gutenberg.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Регистрация кастомной категории для блоков Webdmitriev.
 */
add_filter( 'block_categories_all', 'webdmitriev_kz_add_block_category', 10, 2 );
function webdmitriev_kz_add_block_category( $categories, $editor_context ) {
  array_unshift(
    $categories,
    [
      'slug'  => 'webdmitriev-kz',
      'title' => __( 'Казахстан', 'webdmitriev-kz' ),
      'icon'  => 'star-filled',
    ]
  );
  return $categories;
}

/**
 * 2. Фильтрация разрешенных блоков в Gutenberg.
 */
add_filter( 'allowed_block_types_all', 'webdmitriev_kz_include_blocks', 20, 2 );
function webdmitriev_kz_include_blocks( $allowed_blocks, $editor_context ) {
  $my_plugin_blocks = [
    'webdmitriev/simple-box',
    'webdmitriev/contacts',
  ];

  if ( is_array( $allowed_blocks ) ) {
    return array_merge( $allowed_blocks, $my_plugin_blocks );
  }

  return $allowed_blocks;
}