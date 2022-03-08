<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  // ログインチェック
  $session = new SessionService();
  if (!$session->isLogined()) {
    // ログインしていない場合はトップへ戻す
    header('location: login.php');
    exit;
  }

  $logined = $session->getSessionData();
  $isMgr = $logined[MGRFLG];

  // 管理者チェック
  if (!$isMgr) {
    header('Location: ../routes/detail.php?no='.$logined[NO]);
    exit;
  }

  $htmlFile = $_ENV['APP_ROOT_PATH'].'/html/list.html';

  // 社員データ取得
  $emps = null;
  try {
    $emps = EmpService::getAll(NO, NAME, DEPT, ENT_YEAR);
  } catch (DBConnectException $e) {
    $e->resetApp();
    exit;
  }
  
  $empTrs = '';
  foreach ($emps as $emp) {
    $empTrs .=
      "<tr class=\"item-row\" onclick=\"goDetail(".$emp->getProp(NO).")\">\n".
        "<td nowrap>".$emp->getProp(NO)."</td>\n".
        "<td nowrap>".htmlspecialchars($emp->getProp(NAME), ENT_QUOTES, 'UTF-8')."</td>\n".
        "<td nowrap>".$emp->getProp(DEPT)."</td>\n".
        "<td nowrap>".$emp->getProp(ENT_YEAR)."年</td>\n".
      "</tr>\n";
  }

  $replace = array(
    phpMarker('app-root')   => prefix().pathFromDocRoot(),
    phpMarker('login-user') => htmlspecialchars($logined[NAME], ENT_QUOTES, 'UTF-8'),
    phpMarker('emp-trs')    => $empTrs
  );

  if (is_readable($htmlFile)) {
    $fp = fopen($htmlFile, 'r');
    while (!feof($fp)) {
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
