<?php

  define('SESSION_PATH', getAppRoot().'/.session');
  define('USER_ID', 'userId');
  define('NEW_EMP_DATA', 'newEmpData');

  function reloadLoginStatus() {
    session_save_path(SESSION_PATH);
    session_start();
    if (isset($_SESSION[USER_ID])) {
      session_regenerate_id();
      return array(
        'userId' => getUserId()
      );
    }
    return null;
  }

  function resetSession() {
    if (isset($_SESSION)) {
      unset($_SESSION[USER_ID]);
      unset($_SESSION[NEW_EMP_DATA]);
      return TRUE;
    }
    return FALSE;
  }

  function removeSession() {
    session_destroy();
  }

  function saveUserId($id) {
    $_SESSION[USER_ID] = $id;
    return TRUE;
  }

  function getUserId() {
    return $_SESSION[USER_ID];
  }

  function saveNewEmpData($data) {
    $_SESSION[NEW_EMP_DATA] = $data;
    return TRUE;
  }

  function getNewEmpData() {
    if (isset($_SESSION[NEW_EMP_DATA])) {
      return $_SESSION[NEW_EMP_DATA];
    }
    return null;
  }

?>
