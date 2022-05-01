<?php

  require_once __DIR__.'/../const/sqlConst.php';

  class EvalModel {

    private $evalInfo = array(
      /* 社員評価情報 */
      NO              => null,
      NAME            => null,
      EVAL_YEAR       => null,
      GOAL            => null,
      FST_RES         => null,
      SCD_RES         => null,
      TOTAL_RES       => null,
      FST_EVAL        => null,
      FST_EVALUATOR   => null,
      SCD_EVAL        => null,
      SCD_EVALUATOR   => null,
      TOTAL_EVAL      => null,
      TOTAL_EVALUATOR => null
    );

    public function __construct($evalInfo = null) {
      if ($evalInfo != null) {
        foreach (array_keys($this->evalInfo) as $k) {
          $this->evalInfo[$k] = isset($evalInfo[$k]) ? $evalInfo[$k] : null;
        }
      }
    }

    public function propKeys() {
      $rst = array();
      foreach(array_keys($this->evalInfo) as $k) {
        if (!is_null($this->getProp($k))) array_push($rst, $k);
      }
      return $rst;
    }

    public function setProp($key, $val) {
      if (array_key_exists($key, $this->evalInfo)) {
        $this->evalInfo[$key] = $val;
      }
    }

    public function getProp($key) {
      return array_key_exists($key, $this->evalInfo) ? $this->evalInfo[$key] : null;
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
