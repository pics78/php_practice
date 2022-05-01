<?php

  require_once __DIR__.'/../utils/pathUtil.php';
  require_once __DIR__.'/../services/sessionService.php';

  class DBConnectException extends Exception {
    public $_PDOException = null;

    public function __construct(PDOException $_PDOException) {
      $this->_PDOException = $_PDOException;
      parent::__construct();
    }

    // セッション削除してログイン画面に戻る
    public function resetApp() {
      $session = new SessionService();
      $session->destroy();
      header('location: '.prefix().pathFromDocRoot().'/php/routes/login.php?err=connection_error');
    }
  }

?>
