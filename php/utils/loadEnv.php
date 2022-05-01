<?php

  // 環境変数ファイルの読み込み
  require_once __DIR__.'/../../vendor/autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../.private');
  $dotenv->load();

?>
