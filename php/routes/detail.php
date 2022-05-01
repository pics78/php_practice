<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../models/empModel.php'; 
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../services/renderService.php';
  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  // ログイン状態チェック
  $session = new SessionService();
  if (!$session->isLogined()) {
    // ログインしていない場合はトップへ戻す
    header('location: login.php');
    exit;
  }

  $rs = new RenderService('detail');
  $logined = $session->getSessionData();

  $isMgr = $session->isMgr();
  $isNew = !isset($_GET['no']);
  if (!$isMgr && $isNew) {
    // 管理者以外は新規登録画面への遷移をブロック
    header('location: detail.php?no='.$logined[NO]);
    exit;
  }

  $isMember = $session->isMember();
  if ($isMember && $logined[NO] != $_GET['no']) {
    // メンバーの場合は自分以外の社員詳細画面への遷移をブロック
    header('location: detail.php?no='.$logined[NO]);
    exit;
  }

  // 自分自身の詳細画面へ遷移
  $isSelf = !$isNew ? $_GET['no'] == $logined[NO] : false;

  $rs->pushItem('login-user', $logined[NAME], true)
     ->pushItem('nomgr-disabled', disabledif(!$isMgr))
     ->pushItem('nomgr-hide', hideif(!$isMgr))
     ->pushItem('member-hide', hideif($isMember))
     ->pushItem('new-hide', hideif($isNew))
     ->pushItem('del-hide', hideif($isNew || (!$isNew && !$isMgr) || ($isMgr && $isSelf)));

  // 社員データ取得
  $empInfo = null;
  if ($isNew) {
    $empInfo = new EmpModel();
  } else {
    $empInfo = EmpService::selectByNo($_GET['no'],
      NO, NAME, DEPT_ID, AUTHFLG, ENT_YEAR, ENT_MON, ENT_DAY, LEV_YEAR, LEV_MON, LEV_DAY, ADDRESS);
  }

  $rs->pushItem('target-no', $empInfo->getProp(NO))
     ->pushItem('target-pass', sayif('********', !$isNew))
     ->pushItem('target-name', $empInfo->getProp(NAME), true)
     ->pushItem('target-address', $empInfo->getProp(ADDRESS), true);

  // 所属部optionタグ作成
  $empDeptId = $empInfo->getProp(DEPT_ID);
  $deptOptions = implode("\n",
    array_map(function($d) use ($empDeptId) {
      $deptId   = $d[DEPT_ID];
      $deptName = $d[DEPT_NAME];
      $selected = $deptId === $empDeptId ? 'selected' : '';
      return "<option value=\"$deptId\" $selected>$deptName</option>";
    }, EmpService::getDeptList())
  );
  $rs->pushItem('target-dept-options', $deptOptions);

  // 権限optionタグ作成
  $userAuthId = $empInfo->getProp(AUTHFLG);
  $authOptions = implode("\n",
    array_map(function($a) use ($userAuthId) {
      $authId   = $a[AUTH_ID];
      $authName = $a[AUTH_NAME];
      $selected = $authId == $userAuthId ? 'selected' : '';
      return "<option value=\"$authId\" $selected>$authName</option>";
    }, EmpService::getAuthList())
  );
  $rs->pushItem('target-auth-options', $authOptions);

  // 入社年
  $entYear = $empInfo->getProp(ENT_YEAR);
  $entMon  = $empInfo->getProp(ENT_MON);
  $entDay  = $empInfo->getProp(ENT_DAY);
  $entDate = dateStr($entYear, $entMon, $entDay);
  $rs->pushItem('target-ent-date', $entDate);

  // 退社年
  $levDate = '';
  if ($empInfo->getProp(LEV_YEAR) != null) {
    $levYear = $empInfo->getProp(LEV_YEAR);
    $levMon  = $empInfo->getProp(LEV_MON);
    $levDay  = $empInfo->getProp(LEV_DAY);
    $levDate = dateStr($levYear, $levMon, $levDay);
  }
  $rs->pushItem('target-lev-date', $levDate);

  // 評価情報関連の処理
  $yearList = getYears(3);
  $createdYears = array_map(function($em) {
    return $em->getProp(EVAL_YEAR);
  }, EmpService::getEvalByNo($empInfo->getProp(NO), $yearList, EVAL_YEAR));

  $rs->pushItem('app-root', prefix().pathFromDocRoot())
     ->pushItem('form-action-name', $isNew ? 'create' : 'update')
     ->pushItem('create-eval-disabled', disabledif(in_array($yearList[0], $createdYears)))
     ->pushItem('current-year', $yearList[0])
     ->pushItem('eval-year1', $yearList[0])
     ->pushItem('eval-year1-disabled', disabledif(!in_array($yearList[0], $createdYears)))
     ->pushItem('eval-year2', $yearList[1])
     ->pushItem('eval-year2-disabled', disabledif(!in_array($yearList[1], $createdYears)))
     ->pushItem('eval-year3', $yearList[2])
     ->pushItem('eval-year3-disabled', disabledif(!in_array($yearList[2], $createdYears)))
     ->render();

?>
