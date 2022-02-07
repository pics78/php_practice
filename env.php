<?php

  // 環境情報設定

  function getAppRoot() {
    return __DIR__;
  }

  function getAppPathFromDocRoot() {
    return isset($_SERVER) ?
      str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], '', getAppRoot()) : getAppRoot();
  }

  function getMyPrefix() {
    return isset($_SERVER) ? $_SERVER['CONTEXT_PREFIX'] : '';
  }

?>
