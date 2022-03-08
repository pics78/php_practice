<?php

  require_once __DIR__.'/loadEnv.php';

  # Apacheドキュメントルートからの絶対パス
  function pathFromDocRoot() {
    return isset($_SERVER) ?
      str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], '', $_ENV['APP_ROOT_PATH']) :
      $_ENV['APP_ROOT_PATH'];
  }

  # 自分の公開ディレクトリに付与されたプレフィックス
  function prefix() {
    return isset($_SERVER) ? $_SERVER['CONTEXT_PREFIX'] : '';
  }

?>
