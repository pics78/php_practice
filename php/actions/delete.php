<?php

  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  // [TODO]
  // 削除するレコードが権限ありの場合,
  // 権限ありレコード数が0にならないように最後の1人は削除しないようにする

  if (isset($_POST[NO])) {
    $targetNo = $_POST[NO];
    try {
      EmpService::deleteByNo($targetNo);
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }
  }
  header('location: ../routes/list.php');

?>
