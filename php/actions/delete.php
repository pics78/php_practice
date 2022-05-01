<?php

  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  if (isset($_POST[NO])) {
    $targetNo = $_POST[NO];
    try {
      EmpService::deleteBy(new EmpModel([NO => $targetNo]));
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }
  }
  header('location: ../routes/list.php');

?>
