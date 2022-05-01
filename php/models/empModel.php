<?php

  require_once __DIR__.'/../const/sqlConst.php';

  class EmpModel {

    private $empInfo = array(
      /* 社員基本情報 */
      NO        => null,
      PASS      => null,
      NAME      => null,
      DEPT_ID   => null,
      DEPT_NAME => null,
      AUTHFLG   => null,
      ENT_YEAR  => null,
      ENT_MON   => null,
      ENT_DAY   => null,
      LEV_YEAR  => null,
      LEV_MON   => null,
      LEV_DAY   => null,
      ADDRESS   => null
    );

    public function __construct($empInfo = null) {
      if ($empInfo != null) {
        foreach (array_keys($this->empInfo) as $k) {
          $this->empInfo[$k] = isset($empInfo[$k]) ? $empInfo[$k] : null;
        }
      }
    }

    public function propKeys() {
      $rst = array();
      foreach(array_keys($this->empInfo) as $k) {
        if (!is_null($this->getProp($k))) array_push($rst, $k);
      }
      return $rst;
    }

    public function setProp($key, $val) {
      if (array_key_exists($key, $this->empInfo)) {
        $this->empInfo[$key] = $val;
      }
    }

    public function getProp($key) {
      return array_key_exists($key, $this->empInfo) ? $this->empInfo[$key] : null;
    }

    public function removeProps(...$keys) {
      foreach ($keys as $k) {
        $this->setProp($k, '');
      }
    }

    public function clearProp($key) {
      $this->setProp($key, null);
    }
  }

?>
