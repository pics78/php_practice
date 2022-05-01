<?php

  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../models/evalModel.php';
  require_once __DIR__.'/../services/empService.php';
  require_once __DIR__.'/../connect/dbConnectException.php';

  $res = false;
  $evalName = null;

  if (isset($_POST[EVAL_DATA])) {
    $evalData = $_POST[EVAL_DATA];

    if (isset($evalData[NO]) && isset($evalData[TARGET_YEAR]) && isset($evalData[TYPE])) {
      $empEval = new EvalModel(array(
        NO => $evalData[NO],
        EVAL_YEAR => $evalData[TARGET_YEAR],
        $evalData[TYPE] => $evalData[CONTENT]
      ));

      if (isset($evalData[EVAL_NO])) {
        $evaluator = EmpService::selectByNo($evalData[EVAL_NO], NAME, AUTHFLG);
        if ($evaluator->getProp(AUTHFLG) < 2) {
          $evalName = $evaluator->getProp(NAME);
          $empEval->setProp($evalData[TYPE].'uator', $evalData[EVAL_NO]);
        }
      }
      $res = EmpService::updateEval($empEval);
    }
  }

  header('Content-type: application/json; charset=UTF-8');
  $resultData = ['result' => $res ? 'success' : 'fail'];
  if (!is_null($evalName)) $resultData['evaluator'] = $evalName;

  echo json_encode($resultData);
  exit;

?>
