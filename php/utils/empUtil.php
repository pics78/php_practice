<?php

  define('EMP_JSON_PATH', getAppRoot().'/php/data/emp.json');

  /**
   * 社員データ取得
   */
  function getEmpList($jsonPass = EMP_JSON_PATH, $key = 'EMP_LIST') {
    // JSONファイル読み込み
    $jsonStr = mb_convert_encoding(
        file_get_contents($jsonPass), 'UTF8', 'ASCII, JIS, UTF-8, EUC-JP, SJIS-WIN');
    // ファイル内容をデコード
    return json_decode($jsonStr, true)[$key];
  }

?>
