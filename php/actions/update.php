<?php

  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  if (isset($_POST[EDIT_DATA])) {
    $emp = new EmpModel($_POST[EDIT_DATA]);
    $editData = $_POST[EDIT_DATA];
    $res = false;

    if (preg_match("/^(?=.*\*).*$/", $emp->getProp(PASS))) {
      $emp->clearProp(PASS);
    } else if (!is_password_vaild($emp->getProp(PASS))) {
      error_action($emp->getProp(NO));
      exit;
    }

    if (mb_strlen($emp->getProp(NAME), 'UTF-8') > 20 || is_null($emp->getProp(DEPT_ID))) {
      error_action($emp->getProp(NO));
      exit;
    }

    if (is_date_valid($editData[ENT_DATE])) {
      $entDate = explode('-', $editData[ENT_DATE]);
      $emp->setProp(ENT_YEAR, $entDate[0]);
      $emp->setProp(ENT_MON,  $entDate[1]);
      $emp->setProp(ENT_DAY,  $entDate[2]);
    } else {
      error_action($emp->getProp(NO));
      exit;
    }

    if (is_date_valid($editData[LEV_DATE])) {
      $levDate = explode('-', $editData[LEV_DATE]);
      $emp->setProp(LEV_YEAR, $levDate[0]);
      $emp->setProp(LEV_MON,  $levDate[1]);
      $emp->setProp(LEV_DAY,  $levDate[2]);
    } else {
      $emp->removeProps(LEV_YEAR, LEV_MON, LEV_DAY);
    }

    $res  = false;
    try {
      $res = EmpService::update($emp);
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }

    if ($res) {
      $session = new SessionService();
      if ($session->getSessionData(NO) == $emp->getProp(NO)) {
        $session->updateName($emp->getProp(NAME));
      }
      header('location: ../routes/list.php');
    } else {
      error_action($emp->getProp(NO));
    }
  }

  function is_password_vaild($pass) {
    return preg_match("/^[-0-9A-Za-z_]{1,8}$/", $pass);
  }

  function is_date_valid($date) {
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date);
  }

  function error_action($no) {
    $path = '../routes/detail.php'.(empty($no) ? '' : "?no=$no");
    header("location: $path");
  }

?>
