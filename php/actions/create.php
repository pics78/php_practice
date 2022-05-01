<?php

  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  if (isset($_POST[EDIT_DATA])) {
    $emp = new EmpModel($_POST[EDIT_DATA]);
    $editData = $_POST[EDIT_DATA];

    if (!is_password_vaild($emp->getProp(PASS))) {
      error_action();
      exit;
    }

    if (mb_strlen($emp->getProp(NAME), 'UTF-8') > 20 || is_null($emp->getProp(DEPT_ID))) {
      error_action();
      exit;
    }

    if (is_date_valid($editData[ENT_DATE])) {
      $entDate = explode('-', $editData[ENT_DATE]);
      $emp->setProp(ENT_YEAR, $entDate[0]);
      $emp->setProp(ENT_MON,  $entDate[1]);
      $emp->setProp(ENT_DAY,  $entDate[2]);
    } else {
      error_action();
      exit;
    }

    if (is_date_valid($editData[LEV_DATE])) {
      $levDate = explode('-', $editData[LEV_DATE]);
      $emp->setProp(LEV_YEAR, $levDate[0]);
      $emp->setProp(LEV_MON,  $levDate[1]);
      $emp->setProp(LEV_DAY,  $levDate[2]);
    }

    $res  = false;
    try {
      $res = EmpService::create($emp);
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }

    if ($res) {
      header('location: ../routes/list.php');
    } else {
      error_action();
    }
  }
  // 年度が送られてきたら評価情報レコードの作成を行う
  else if (isset($_POST[TARGET_YEAR])) {
    $no   = $_POST[NO];
    $year = $_POST[TARGET_YEAR];
    $res  = false;
    try {
      if (count(EmpService::getEvalByNo($no, [$year], EVAL_YEAR)) == 0) {
        $res = EmpService::createEval($no, $year);
      } else {
        // すでに今年の評価情報作成済み
        $res  = true;
      }
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }

    if ($res) {
      //作成した評価情報の入力画面へ遷移する
      header( "location: ../routes/evaluation.php?no=$no&y=$year");
    } else {
      // TODO: エラー画面的なものに遷移させたい
    }
  }

  function is_password_vaild($pass) {
    return preg_match("/^[-0-9A-Za-z_]{1,8}$/", $pass);
  }  

  function is_date_valid($date) {
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date);
  }

  function error_action() {
    $path = '../routes/detail.php';
    header("location: $path");
  }

?>
