<?php

  require_once __DIR__.'/../const/sqlConst.php';

  function SELECT(...$items) {
    return 'select '.implode(', ', $items).' ';
  }

  function SELECT_LIST($list) {
    return call_user_func_array('SELECT', $list);
  }

  function FROM($mst, $joined = null, $keypair = null) {
    $s = "from $mst ";
    if ($joined != null && $keypair != null && count($keypair) == 2) {
      $s .= "inner join $joined ".
            "on $mst.".$keypair[0].' = '."$joined.".$keypair[1].' ';
    }
    return $s;
  }

  function DELETE_FROM($mst) {
    return 'delete '.FROM($mst);
  }

  function UPDATE_TO_SET($mst, $cols) {
    $presets = array_map(function($col) {
      return "$col = :$col";
    }, $cols);
    return 'update '.$mst.' set '.implode(', ', $presets).' ';
  }

  function INSERT_VALUES($mst, $cols) {
    $prevals = array_map(function($col) {
      return ":$col";
    }, $cols);
    return 'insert into '.$mst.' ('.implode(', ', $cols).') values ('.implode(', ', $prevals).')';
  }

  function WHERE($conditions) {
    return "where $conditions ";
  }

  function IS_MATCH($item) {
    return "$item = :$item";
  }

  function MAX_NUM($item) {
    return "max($item)";
  }

  define('ASC', 'asc');
  define('DESC', 'desc');

  function ORDER_BY($items, $order = ASC) {
    $target = null;
    if (is_string($items)) {
      $target = $items;
    } else if (is_array($items)) {
      $target = implode(', ', $items);
    }

    return $target != null ? "order by $target $order " : '';
  }
    

?>
