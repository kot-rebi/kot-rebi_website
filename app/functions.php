<?php
/**
 * Undocumented function
 *
 * @return void
 */
function getHeaderTitle() {
  if (isset($GLOBALS['headerTitleText']) && !empty($GLOBALS['headerTitleText'])){
    return htmlspecialchars($GLOBALS['headerTitleText']);
  }

  return 'ゲームブログ';
}