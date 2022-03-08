<?php

  require_once __DIR__.'/../const/sqlConst.php';

  class EmpModel {

    private $empInfo = array(
      NO        => null,
      NAME      => '',
      DEPT      => '',
      MGRFLG    => null,
      ENT_YEAR  => null,
      ENT_MON   => null,
      ENT_DAY   => null,
      LEV_YEAR  => null,
      LEV_MON   => null,
      LEV_DAY   => null,
      ADDRESS   => ''
    );

    public function __construct($empInfo = null) {
      if ($empInfo != null) {
        $props = array_keys($this->empInfo);
        foreach ($props as $prop) {
          $this->empInfo[$prop] = isset($empInfo[$prop]) ? $empInfo[$prop] : null;
        }
      }
    }

    public function setProp($key, $val) {
      if (array_key_exists($key, $this->empInfo)) {
        $this->empInfo[$key] = $val;
      }
    }

    public function getProp($key) {
      return array_key_exists($key, $this->empInfo) ? $this->empInfo[$key] : null;
    }   
  }

?>
