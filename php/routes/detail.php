<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../models/empModel.php'; 
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  // ログイン状態チェック
  $session = new SessionService();
  if (!$session->isLogined()) {
    // ログインしていない場合はトップへ戻す
    header('location: login.php');
    exit;
  }

  $logined = $session->getSessionData();
  $isMgr = $logined[MGRFLG];
  $isNew = !isset($_GET['no']);

  if (!$isMgr && ($isNew || $logined[NO] != $_GET['no'])) {
    header('location: detail.php?no='.$logined[NO]);
    exit;
  }

  $htmlFile = $_ENV['APP_ROOT_PATH'].'/html/detail.html';

  // 社員データ取得
  $empInfo = null;
  if ($isNew) {
    $empInfo = new EmpModel();
  } else {
    try {
      $empInfo = EmpService::selectByNo($_GET['no'],
        NO, NAME, DEPT, MGRFLG, ENT_YEAR, ENT_MON, ENT_DAY, LEV_YEAR, LEV_MON, LEV_DAY, ADDRESS);
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }
  }

  $targetDept = $empInfo->getProp(DEPT);
  $deptOptions = null;
  try {
    $deptOptions = implode("\n",
      array_map(function($dept) use ($targetDept) {
        $selected = $dept === $targetDept ? 'selected' : '';
        return "<option value=\"$dept\" $selected>$dept</option>";
      }, EmpService::getDeptList())
    );
  } catch (DBConnectException $e) {
    $e->resetApp();
    exit;
  }

  $entDate = dateStr($empInfo->getProp(ENT_YEAR), $empInfo->getProp(ENT_MON), $empInfo->getProp(ENT_DAY));
  $levDate = $empInfo->getProp(LEV_YEAR) != null ?
    dateStr($empInfo->getProp(LEV_YEAR), $empInfo->getProp(LEV_MON), $empInfo->getProp(LEV_DAY)) : '';

  $replace = array(
    phpMarker('app-root')             => prefix().pathFromDocRoot(),
    phpMarker('login-user')           => htmlspecialchars($logined[NAME], ENT_QUOTES, 'UTF-8'),
    phpMarker('btn-for-mgr')          => $isMgr ? '' : 'hide',
    phpMarker('target-no')            => $empInfo->getProp(NO),
    phpMarker('target-pass')          => $isNew ? '' : '********',
    phpMarker('target-name')          => htmlspecialchars($empInfo->getProp(NAME), ENT_QUOTES, 'UTF-8'),
    phpMarker('target-dept-options')  => $deptOptions,
    phpMarker('target-dept')          => $targetDept,
    phpMarker('target-mgr-checked')   => $empInfo->getProp(MGRFLG) ? 'checked' : '',
    phpMarker('target-mgr-flg')       => $empInfo->getProp(MGRFLG),
    phpMarker('target-ent-date')      => $entDate,
    phpMarker('target-lev-date')      => $levDate,
    phpMarker('target-address')       => htmlspecialchars($empInfo->getProp(ADDRESS), ENT_QUOTES, 'UTF-8'),
  );
  
  if (is_readable($htmlFile)) {
    $fp = fopen($htmlFile, 'r');
    while (!feof($fp)) {
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
