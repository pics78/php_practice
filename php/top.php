<?php

  // 依存モジュール
  $DependentModules['utils'] = array('sessionUtil');
  require 'loadModule.php';

  // ログイン状態チェック
  $status = reloadLoginStatus();
  if ($status['userId'] != null) {
    // ログイン中の場合は社員一覧画面へリダイレクト
    header('location: list.php');
    exit;
  }

  $htmlFile = getAppRoot().'/html/top.html'; 

  // エラーコード
  define('LOGIN_ERR', 'LE');

  if (is_readable($htmlFile)) {
    $errMsgStatus = 'hide';
    if (isset($_GET['err'])) {
      $errMsgStatus = $_GET['err'] == LOGIN_ERR ? '' : 'hide';
    }

    $fp = fopen($htmlFile, 'r');
    while (!feof($fp)) {
      $replace = array(
        '$$PHP-check-err$$' => $errMsgStatus,
        '$$PHP-app-root$$'  => getMyPrefix().getAppPathFromDocRoot()
      );
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
