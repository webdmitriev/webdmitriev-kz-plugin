<?php

if ( ! function_exists( 'webdmitriev_is_lang' ) ) {
  /**
   * Проверяет, соответствует ли текущий язык ожидаемому.
   *
   * @param string $current Текущий язык, например 'RU' или 'KZ'.
   * @param string $lang    Язык для сравнения, по умолчанию 'KZ'.
   * @return bool
   */
  function webdmitriev_is_lang( string $current, string $lang = 'KZ' ): bool {
    return strtoupper( $current ) === strtoupper( $lang );
  }
}