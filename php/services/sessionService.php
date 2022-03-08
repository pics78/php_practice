<?php

  require_once __DIR__.'/../const/sessionConst.php';
  require_once __DIR__.'/../const/sqlConst.php';

  class SessionService {

    public function __construct() {
      session_save_path(SESSION_SAVE_PATH);
      session_start();
    }

    public function setSessionData($sessionData) {
      $_SESSION[NO]     = isset($sessionData[NO])     ? $sessionData[NO]     : null;
      $_SESSION[NAME]   = isset($sessionData[NAME])   ? $sessionData[NAME]   : null;
      $_SESSION[MGRFLG] = isset($sessionData[MGRFLG]) ? $sessionData[MGRFLG] : null;
    }

    public function isLogined() {
      return isset($_SESSION[NO]) && isset($_SESSION[NAME]) && isset($_SESSION[MGRFLG]);
    }

    public function destroy() {
      session_destroy();
    }

    public function getSessionData() {
      return array(
       NO     => $_SESSION[NO],
       NAME   => $_SESSION[NAME],
       MGRFLG => $_SESSION[MGRFLG]
      );
    }
  }

?>
