<?php

  // ログイン認証処理

  define('LOGIN_ID', 'testid');        // ID
  define('LOGIN_PASS', 'testpass'); // パスワード

  if (isset($_POST['login'])) {
    $loginData = $_POST['login'];
    if ($loginData['id'] == LOGIN_ID && $loginData['pass'] == LOGIN_PASS) {
      // ログイン成功
      $DependentModules['utils'] = array('sessionUtil');
      require 'loadModule.php';

      if (reloadLoginStatus() != null) {
        // すでにログイン中の場合は前の情報を破棄
        resetSession();
      }

      saveUserId(LOGIN_ID);
      header('Location: ../list.php');
      exit;
    }
  }

  // POSTデータがない または ログイン情報ミス
  header('Location: ../top.php?err=LE');

?>
