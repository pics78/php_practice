<?php

  function phpMarker($name) {
    return '$$PHP-'.$name.'$$';
  }

  function dateStr($year, $mon, $day) {
    return implode('-', array(
      str_pad($year, 4/*桁*/, 0, STR_PAD_LEFT),
      str_pad($mon,  2/*桁*/, 0, STR_PAD_LEFT),
      str_pad($day,  2/*桁*/, 0, STR_PAD_LEFT)
    ));
  }

?>
