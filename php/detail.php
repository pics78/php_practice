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

  $htmlFile = getAppRoot().'/html/detail.html';

  if (!isset($_GET['id'])) {
    // 社員IDがないと表示する内容がわからないので社員一覧に戻す
    header('Location: list.php');
    exit;
  }

  // 社員データ取得
  $EMP_LIST = getEmpList();

  // セッションに新社員データが保持されている場合は配列末尾に追加
  $newEmpData = getNewEmpData();
  if ($newEmpData != null) {
    array_push($EMP_LIST, $newEmpData);
  }

  $targetId = $_GET['id'];
  $empIdList = array_column($EMP_LIST, 'id');
  
  $targetKey = array_search($targetId, $empIdList);
  if ($targetKey === FALSE) {
    // 存在しないIDのため社員一覧に戻す
    header('Location: list.php');
    exit;
  }

  // 表示する社員情報の取得
  $empInfo = $EMP_LIST[$targetKey];

  if (is_readable($htmlFile)) {
    $fp = fopen($htmlFile, 'r');
    $appRootInServer = getMyPrefix().getAppPathFromDocRoot();
    while (!feof($fp)) {
      $replace = array(
        '$$PHP-app-root$$' => $appRootInServer,
        '$$PHP-emp-id$$'   => $empInfo['id'],
        '$$PHP-emp-name$$' => $empInfo['name'],
        '$$PHP-emp-mail$$' => $empInfo['mail'],
        '$$PHP-emp-tel$$'  => $empInfo['tel'],
        '$$PHP-emp-dept$$' => $empInfo['dept'],
        '$$PHP-emp-pjt$$'  => $empInfo['pjt']
      );
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
