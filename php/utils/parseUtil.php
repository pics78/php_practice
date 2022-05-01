<?php

  function dateStr($year, $mon, $day) {
    if ($year != null && $mon != null && $day != null) {
      return implode('-', array(
        str_pad($year, 4/*桁*/, 0, STR_PAD_LEFT),
        str_pad($mon,  2/*桁*/, 0, STR_PAD_LEFT),
        str_pad($day,  2/*桁*/, 0, STR_PAD_LEFT)
      ));
    }
    return null;
  }

  /**
   * 直近n年間の西暦をリストで返す
   */
  function getYears($n = 1) {
    $currentYear = date('Y');
    $yearList = [];
    for ($i=0; $i<$n; $i++) {
      array_push($yearList, $currentYear - $i);
    }
    return $yearList;
  }

  function sayif($w, $c) {
    return $c ? $w : '';
  }

  function hideif($c) {
    return sayif('hide', $c);
  }

  function disabledif($c) {
    return sayif('disabled', $c);
  }

?>
