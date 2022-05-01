<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../utils/parseUtil.php';
  require_once __DIR__.'/../const/httpConst.php';
  require_once __DIR__.'/../services/renderService.php';

  $rs = new RenderService('login');

  // エラー表示有無
  $rs->pushItem('check-err', isset($_GET['err']) && $_GET['err'] == LOGIN_ERR ? '' : 'hide')
     ->pushItem('app-root', prefix().pathFromDocRoot())
     ->render();

?>
