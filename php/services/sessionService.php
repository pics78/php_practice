<?php

  require_once __DIR__.'/../const/sessionConst.php';
  require_once __DIR__.'/../const/sqlConst.php';

  class SessionService {

    public function __construct() {
      session_save_path(SESSION_SAVE_PATH);
      session_start();
    }

    public function setSessionData($sessionData) {
      $_SESSION[NO]      = isset($sessionData[NO])      ? $sessionData[NO]      : null;
      $_SESSION[NAME]    = isset($sessionData[NAME])    ? $sessionData[NAME]    : null;
      $_SESSION[AUTHFLG] = isset($sessionData[AUTHFLG]) ? $sessionData[AUTHFLG] : null;
    }

    public function updateName($name) {
      if (isset($name)) $_SESSION[NAME] = $name;
    }

    public function isLogined() {
      return isset($_SESSION[NO]) && isset($_SESSION[NAME]) && isset($_SESSION[AUTHFLG]);
    }

    public function destroy() {
      session_destroy();
    }

    public function getSessionData($key = null) {
      return is_null($key) ? array(
       NO      => $_SESSION[NO],
       NAME    => $_SESSION[NAME],
       AUTHFLG => $_SESSION[AUTHFLG]
      )
      :
      $_SESSION[$key];
    }

    public function isMgr() {
      return $_SESSION[AUTHFLG] == FLG_MGR;
    }

    public function isEvaluator() {
      return $_SESSION[AUTHFLG] == FLG_EVR;
    }

    public function isMember() {
      return $_SESSION[AUTHFLG] == FLG_MBR;
    }
  }

?>
