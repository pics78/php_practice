<?php

  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  if (isset($_POST[EDIT_DATA])) {
    $editData = $_POST[EDIT_DATA];
    $res = false;

    // 入力チェック
    if (
      !(isset($editData[PASS])) ||
      !(isset($editData[NAME]) && mb_strlen($editData[NAME], 'UTF-8') <= 20) ||
      !(isset($editData[DEPT])) ||
      !(isset($editData[ENT_DATE]) && preg_match("/^\d{4}-\d{2}-\d{2}$/", $editData[ENT_DATE]))
    ) {
      // 入力エラー
      error_action($editData[NO]);
      exit;
    }

    $isNew = empty($editData[NO]);
    $res  = false;
    if ($isNew) {
      // 新規登録処理

      if (!is_password_vaild($editData[PASS])) {
        // バリデーションエラー
        error_action($editData[PASS]);
        exit;
      }

      $entDate = explode('-', $editData[ENT_DATE]);
      $levDate = null;
      if (isset($editData[LEV_DATE]) && is_date_valid($editData[LEV_DATE])) {
        $levDate = explode('-', $editData[LEV_DATE]);
      }

      $address = isset($editData[ADDRESS]) ? $editData[ADDRESS] : null;

      try {
        $res = EmpService::create(array(
          PASS      => $editData[PASS],
          NAME      => $editData[NAME],
          DEPT      => $editData[DEPT],
          MGRFLG    => isset($editData[MGRFLG]),
          ENT_YEAR  => $entDate[0],
          ENT_MON   => $entDate[1],
          ENT_DAY   => $entDate[2],
          LEV_YEAR  => $levDate != null ? $levDate[0] : null,
          LEV_MON   => $levDate != null ? $levDate[1] : null,
          LEV_DAY   => $levDate != null ? $levDate[2] : null,
          ADDRESS   => $address != null ? $address    : null
        ));
      } catch (DBConnectException $e) {
        $e->resetApp();
        exit;
      }
    } else {
      // 更新処理

      if (!isset($_POST[ORIG_DATA])) {
        // POSTエラー
        error_action($editData[NO]);
        exit;
      }

      $origData = $_POST[ORIG_DATA];
      $updateData = array();

      // 変更した値をチェック
      if (!preg_match("/^(?=.*\*).*$/", $editData[PASS]) && is_password_vaild($editData[PASS])) {
        $updateData[PASS] = $editData[PASS];
      }
      if ($editData[NAME] !== $origData[NAME]) $updateData[NAME] = $editData[NAME];
      if ($editData[DEPT] !== $origData[DEPT]) $updateData[DEPT] = $editData[DEPT];
      if ((isset($editData[MGRFLG]) ? 1 : 0) != $origData[MGRFLG]) $updateData[MGRFLG] = isset($editData[MGRFLG]);
      if ($editData[ENT_DATE] !== $origData[ENT_DATE]) {
        $entDate = explode('-', $editData[ENT_DATE]);
        $updateData[ENT_YEAR] = $entDate[0];
        $updateData[ENT_MON]  = $entDate[1];
        $updateData[ENT_DAY]  = $entDate[2];
      }
      if (isset($editData[LEV_DATE]) && $editData[LEV_DATE] !== $origData[LEV_DATE] && is_date_valid($editData[LEV_DATE])) {
        $levDate = explode('-', $editData[LEV_DATE]);
        $updateData[LEV_YEAR] = $levDate[0];
        $updateData[LEV_MON]  = $levDate[1];
        $updateData[LEV_DAY]  = $levDate[2];
      }
      if ($editData[ADDRESS] !== $origData[ADDRESS]) $updateData[ADDRESS] = $editData[ADDRESS];

      try {
        $res = EmpService::updateToNo($editData[NO], $updateData);
      } catch (DBConnectException $e) {
        $e->resetApp();
        exit;
      }
    }

    if ($res) {
      header('location: ../routes/list.php');
    } else {
      error_action($editData[NO]);
    }
  }

  function is_password_vaild ($pass) {
    return preg_match("/^[-0-9A-Za-z_]{1,8}$/", $pass);
  }

  function is_date_valid ($date) {
    return preg_match("/^\d{4}-\d{2}-\d{2}$/", $date);
  }

  function error_action ($no) {
    $path = '../routes/detail.php'.(empty($no) ? '' : "?no=$no");
    header("location: $path");
  }

?>
