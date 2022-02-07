<?php

  // 依存モジュール
  $DependentModules['utils'] = array('sessionUtil', 'empUtil');
  require 'loadModule.php';

  // ログイン状態チェック
  $status = reloadLoginStatus();
  if ($status == null || $status['userId'] == null) {
    // ログインしていない場合はトップへ戻す
    header('location: top.php');
  }

  $htmlFile = getAppRoot().'/html/new.html';

  // 既存社員データ数取得
  $empCount = count(getEmpList());

  $newEmpId = sprintf('%04d', $empCount+1);
  if (is_readable($htmlFile)) {
    $fp = fopen($htmlFile, 'r');
    $appRootInServer = getMyPrefix().getAppPathFromDocRoot();
    while (!feof($fp)) {
      $replace = array(
        '$$PHP-app-root$$'   => $appRootInServer,
        '$$PHP-new-emp-id$$' => $newEmpId
      );
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
