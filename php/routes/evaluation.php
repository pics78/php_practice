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

  if (!isset($_GET['no']) || !isset($_GET['y'])) {
    // 社員番号と対象年が指定されていない場合はログインユーザーの詳細画面へ
    header('location: detail.php?no='.$logined[NO]);
    exit;
  }

  $rs = new RenderService('evaluation');
  $logined = $session->getSessionData();
  $targetNo   = $_GET['no'];
  $targetYear = $_GET['y'];

  // 自分の評価情報かどうか
  $isMgr = $session->isMgr();
  $isSelf = $logined[NO] == $targetNo;

  if ($session->isMember() && !$isSelf) {
    header('location: evaluation.php?no='.$logined[NO]);
    exit;
  }

  // 評価者(評価書き込み権限)フラグ
  // 評価者権限を持つ社員も自分の評価を書き込むことはできない
  $isEval = $isMgr || ($session->isEvaluator() && !$isSelf);

  $rs->pushItem('login-user', $logined[NAME], true)
     ->pushItem('nomgr-disabled', disabledif(!$isMgr))
     ->pushItem('nooneself-disabled', disabledif(!$isMgr && !$isSelf))
     ->pushItem('nooneself-hide', hideif(!$isMgr && !$isSelf))
     ->pushItem('noeval-disabled', disabledif(!$isEval))
     ->pushItem('noeval-hide', hideif(!$isEval))
     ->pushItem('target-no', $targetNo)
     ->pushItem('evaluator-no', $isEval ? $logined[NO] : '');

  $yearList = getYears(3);
  $createdYears = array_map(function($em) {
    return $em->getProp(EVAL_YEAR);
  }, EmpService::getEvalByNo($targetNo, $yearList, EVAL_YEAR));

  // 評価年度optionタグ作成
  $yearOptions = implode("\n",
    array_map(function($y) use ($targetYear) {
      $selected = $y == $targetYear ? 'selected' : '';
      return "<option value=\"$y\" $selected>".$y."年度</option>";
    }, $createdYears)
  );

  $rs->pushItem('target-year', $targetYear)
     ->pushItem('year-options', $yearOptions);

  // 社員データ取得
  $empInfo = null;
  $evaluators = array();
  $empInfo = EmpService::getEvalByNo($targetNo, [$targetYear], NAME,
    /*[目標]*/ GOAL,
    //       [実績]     [評価]      [評価者]
    /*上期*/ FST_RES,   FST_EVAL,   FST_EVALUATOR,
    /*下期*/ SCD_RES,   SCD_EVAL,   SCD_EVALUATOR,
    /*全体*/ TOTAL_RES, TOTAL_EVAL, TOTAL_EVALUATOR
  )[0];

  $rs->pushItem('target-name', $empInfo->getProp(NAME), true)
     ->pushItem('target-goal', $empInfo->getProp(GOAL), true);

  $fstEvalNo = $empInfo->getProp(FST_EVALUATOR);
  $scdEvalNo = $empInfo->getProp(SCD_EVALUATOR);
  $ttlEvalNo = $empInfo->getProp(TOTAL_EVALUATOR);

  // 評価者番号から評価者の名前を習得
  foreach (array_unique([$fstEvalNo, $scdEvalNo, $ttlEvalNo]) as $no) {
    if ($no != null) {
      $evaluators[$no] = EmpService::selectByNo($no, NAME)->getProp(NAME);
    }
  }

  $rs->pushItem('app-root', prefix().pathFromDocRoot())
     ->pushItem('target-first-result', $empInfo->getProp(FST_RES), true)
     ->pushItem('target-first-eval', $empInfo->getProp(FST_EVAL), true)
     ->pushItem('target-first-evaluator', $fstEvalNo != null ? $evaluators[$fstEvalNo] : '', true)
     ->pushItem('is-first-evaluator-hide', hideif($fstEvalNo == null))
     ->pushItem('target-second-result', $empInfo->getProp(SCD_RES), true)
     ->pushItem('target-second-eval', $empInfo->getProp(SCD_EVAL), true)
     ->pushItem('target-second-evaluator', $scdEvalNo != null ? $evaluators[$scdEvalNo] : '', true)
     ->pushItem('is-second-evaluator-hide', hideif($scdEvalNo == null))
     ->pushItem('target-total-result', $empInfo->getProp(TOTAL_RES), true)
     ->pushItem('target-total-eval', $empInfo->getProp(TOTAL_EVAL), true)
     ->pushItem('target-total-evaluator', $ttlEvalNo != null ? $evaluators[$ttlEvalNo] : '', true)
     ->pushItem('is-total-evaluator-hide', hideif($ttlEvalNo == null))
     ->render();

?>
