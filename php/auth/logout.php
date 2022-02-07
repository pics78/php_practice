<?php

  $DependentModules['utils'] = array('sessionUtil');
  require 'loadModule.php';

  $status = reloadLoginStatus();

  // ログアウト処理
  if ($status != null) {
    removeSession();
    header('Location: ../top.php');
    exit;
  }

  // そもそもログインしていない
  header('Location: ../top.php');

?>
