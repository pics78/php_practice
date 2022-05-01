<?php

  // ログイン認証処理

  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../const/sessionConst.php';
  require_once __DIR__.'/../services/sessionService.php';
  require_once __DIR__.'/../connect/dbConnect.php';
  require_once __DIR__.'/../connect/dbConnectException.php';
  require_once __DIR__.'/../utils/sqlUtil.php';

  if (isset($_POST[INPUT_DATA])) {
    $loginData = $_POST[INPUT_DATA];
    $userNo    = $loginData[INPUT_NO];
    $userPass  = $loginData[INPUT_PASS];
    
    $hashedPass = null;
    try {
      $hashedPass = getHashedPass($userNo);
    } catch (DBConnectException $e) {
      $e->resetApp();
      exit;
    }

    // パスワードの検証
    $isSuccess = $hashedPass != null ? password_verify($userPass, $hashedPass) : false;

    if ($isSuccess) {
      // ログイン成功
      $sessionData = null;
      try {
        $sessionData = getSessionData($userNo);
      } catch (DBConnectException $e) {
        $e->resetApp();
        exit;
      }

      $session = new SessionService();
      $session->setSessionData($sessionData);

      if ($session->isMgr() || $session->isEvaluator()) {
        header('Location: ../routes/list.php');
      } else {
        // 管理者以外は自分の詳細ページへ
        header('Location: ../routes/detail.php?no='.$sessionData[NO]);
      }
      exit;
    }
  }
  
  // ログイン失敗
  // ログイン画面に戻ってエラー表示
  header('Location: ../routes/login.php?err='.LOGIN_ERR);

  function getHashedPass($no) {
    $stmt = DBConnect::stream('prepare',
      SELECT(PASS).
      FROM(TNAME).
      WHERE(IS_MATCH(NO))
    );
    $stmt->bindParam(NO, $no, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() == 1 ? $stmt->fetch(PDO::FETCH_COLUMN) : null;
  }

  function getSessionData($userNo) {
    $stmt = DBConnect::stream('prepare',
      SELECT(NO, NAME, AUTHFLG).
      FROM(TNAME).
      WHERE(IS_MATCH(NO))
    );
    $stmt->bindParam(NO, $userNo, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() == 1 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
  }

?>
