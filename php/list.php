<?php

  // 依存モジュール
  $DependentModules['utils'] = array('sessionUtil', 'empUtil');
  require 'loadModule.php';
 
  // ログインチェック
  $status = reloadLoginStatus();
  if ($status == null || $status['userId'] == null) {
    // ログインしていない場合はトップへ戻す
    header('location: top.php');
  }

  $htmlFile = getAppRoot().'/html/list.html';

  // 社員データ取得
  $EMP_LIST = getEmpList();

  if (is_readable($htmlFile)) {
    
    $newEmpData = getNewEmpData();
    // 新社員情報がPOSTされていた場合
    if (isset($_POST['newEmp'])) {
      $newEmpData = $_POST['newEmp']; // セッションに保持されていた場合も上書き
      saveNewEmpData($newEmpData);
    }

    if ($newEmpData != null) {
      // 新社員情報があれば配列末尾に追加
      array_push($EMP_LIST, array(
        'id'   => $newEmpData['id'],
        'name' => $newEmpData['name'],
        'mail' => $newEmpData['mail'],
        'tel'  => $newEmpData['tel']
      ));
    }

    $fp = fopen($htmlFile, 'r');
    $appRootInServer = getMyPrefix().getAppPathFromDocRoot();
    $isInListTag = FALSE;
    $listTagContents = []; // 社員パネルテンプレートの各行を入れるリスト
    while (!feof($fp)) {
      $line = str_replace('$$PHP-app-root$$', $appRootInServer, fgets($fp));

      // 社員パネルテンプレート行開始
      if (preg_match('/^.*\$\$PHP-list-start\$\$.*$/', $line)) {
        $isInListTag = TRUE;
      // 社員パネルテンプレート行終了
      } else if (preg_match('/^.*\$\$PHP-list-end\$\$.*$/', $line)) {
        $isInListTag = FALSE;
        // テンプレートをもとに各社員情報を置換
        foreach($EMP_LIST as $empInfo) {
          $replace = array(
            '$$PHP-emp-detail-path$$' => 'detail.php?id='.$empInfo['id'],
            '$$PHP-emp-id$$'   => $empInfo['id'],
            '$$PHP-emp-name$$' => $empInfo['name'],
            '$$PHP-emp-mail$$' => $empInfo['mail'],
            '$$PHP-emp-tel$$'  => $empInfo['tel']
          );
          foreach($listTagContents as $listContent) {
            echo strtr($listContent, $replace);
          }
        }
      // 社員パネルテンプレート取得
      } else if ($isInListTag) {
        array_push($listTagContents, $line);
      } else {
        echo str_replace('$$PHP-new-emp-path$$', 'new.php', $line);
      }
    }
    fclose($fp);
  }

?>
