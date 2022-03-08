<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../const/httpConst.php';

  $htmlFile = $_ENV['APP_ROOT_PATH'].'/html/login.html'; 

  // エラー表示有無
  $errMsgStatus = 'hide';
  if (isset($_GET['err'])) {
    $errMsgStatus = $_GET['err'] == LOGIN_ERR ? '' : 'hide';
  }

  $replace = array(
    phpMarker('check-err') => $errMsgStatus,
    phpMarker('app-root')  => prefix().pathFromDocRoot()
  );

  if (is_readable($htmlFile)) {
    $fp = fopen($htmlFile, 'r');
    while (!feof($fp)) {
      echo strtr(fgets($fp), $replace);
    }
    fclose($fp);
  }

?>
