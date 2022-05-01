<?php

  function SELECT(...$items) {
    return 'select '.implode(', ', $items).' ';
  }

  function SELECT_LIST($list) {
    return call_user_func_array('SELECT', $list);
  }

  // join type
  define('INNER', 'inner');
  define('OUTER', 'outer');
  define('RIGHT', 'right');
  define('LEFT', 'left');

  /**
   * tbl: 元テーブル名
   * joined: 結合するテーブル名と条件の連想配列 { string table, string jointype, string[] keypair }
   */
  function FROM($tbl, ...$joined) {
    $s = "from $tbl ";
    foreach ($joined as $j) {
      if ($j != null) {
        $s .= $j['jointype'].' join '.$j['table'].' '.
              "on $tbl.".$j['keypair'][0].' = '.$j['table'].'.'.$j['keypair'][1].' ';
      }
    }
    return $s;
  }

  function getJoinObj($table, $type, $okey, $fkey) {
    return array('table'=>$table, 'jointype'=>$type, 'keypair'=>[$okey, $fkey]);
  }

  function innerJoin($table, $okey, $fkey) {
    return getJoinObj($table, INNER, $okey, $fkey);
  }

  function outerJoin($table, $okey, $fkey) {
    return getJoinObj($table, OUTER, $okey, $fkey);
  }

  function rightJoin($table, $okey, $fkey) {
    return getJoinObj($table, RIGHT, $okey, $fkey);
  }

  function leftJoin($table, $okey, $fkey) {
    return getJoinObj($table, LEFT, $okey, $fkey);
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

  define('_AND_', ' and ');

  function IS_MATCH($item, $table = null) {
    return (!is_null($table) ? "$table." : "").$item." = :$item";
  }

  function IN_VALUES($item, $vals) {
    return $item.' in ('.implode(', ', $vals).')';
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
