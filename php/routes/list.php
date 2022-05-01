<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../services/renderService.php';
  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  // ログインチェック
  $session = new SessionService();
  if (!$session->isLogined()) {
    // ログインしていない場合はトップへ戻す
    header('location: login.php');
    exit;
  }

  $rs = new RenderService('list');
  $logined = $session->getSessionData();

  // 権限チェック
  if ($session->isMember()) {
    // メンバーは社員一覧を閲覧できない
    header('Location: ../routes/detail.php?no='.$logined[NO]);
    exit;
  }

  $rs->pushItem('nomgr-hide', hideif(!$session->isMgr()))
     ->pushItem('login-user', $logined[NAME], true);

  // 社員データ取得
  $emps = null;
  try {
    $emps = EmpService::getAll(NO, NAME, DEPT_NAME, ENT_YEAR);
  } catch (DBConnectException $e) {
    $e->resetApp();
    exit;
  }
  
  // 社員一覧テーブル用HTMLの作成
  $empTrs = '';
  foreach ($emps as $emp) {
    $empTrs .=
      "<tr class=\"item-row\" onclick=\"goDetail(".$emp->getProp(NO).")\">\n".
        "<td nowrap>".$emp->getProp(NO)."</td>\n".
        "<td nowrap>".htmlspecialchars($emp->getProp(NAME), ENT_QUOTES, 'UTF-8')."</td>\n".
        "<td nowrap>".$emp->getProp(DEPT_NAME)."</td>\n".
        "<td nowrap>".$emp->getProp(ENT_YEAR)."年</td>\n".
      "</tr>\n";
  }
  
  $rs->pushItem('emp-trs', $empTrs)
     ->pushItem('app-root', prefix().pathFromDocRoot())
     ->render();

?>
