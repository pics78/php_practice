<?php

  require_once __DIR__.'/../utils/loadEnv.php';

  // セッション保存先
  define('SESSION_SAVE_PATH', $_ENV['APP_ROOT_PATH'].'/.private/.session');

?>
