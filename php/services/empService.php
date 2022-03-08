<?php

  require_once __DIR__.'/../connect/dbConnect.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../utils/sqlUtil.php';

  class EmpService {

    const DATA_TYPES = array(
      NO        => PDO::PARAM_INT,
      PASS      => PDO::PARAM_STR,
      NAME      => PDO::PARAM_STR,
      DEPT      => PDO::PARAM_STR,
      MGRFLG    => PDO::PARAM_INT,
      ENT_YEAR  => PDO::PARAM_INT,
      ENT_MON   => PDO::PARAM_INT,
      ENT_DAY   => PDO::PARAM_INT,
      LEV_YEAR  => PDO::PARAM_INT,
      LEV_MON   => PDO::PARAM_INT,
      LEV_DAY   => PDO::PARAM_INT,
      ADDRESS   => PDO::PARAM_STR
    );

    public static function getDeptList() {
      $stmt = DBConnect::stream('query',
        SELECT(DEPT_NAME).
        FROM(DEPT_MST).
        ORDER_BY(DEPT_ID, ASC)
      );
      return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function selectByNo($id, ...$items) {
      $stmt = DBConnect::stream('prepare',
        SELECT_LIST($items).
        FROM(TNAME).
        WHERE(IS_MATCH(NO))
      );
      $stmt->bindParam(NO, $id, PDO::PARAM_INT);
      $stmt->execute();
      if ($stmt->rowCount() == 1) {
        return new EmpModel($stmt->fetch(PDO::FETCH_ASSOC));
      }
      return null;
    }

    public static function getAll(...$items) {
      $stmt = DBConnect::stream('query',
        SELECT_LIST($items).
        FROM(TNAME).
        ORDER_BY(NO, ASC)
      );
      return array_map(function($fetchedObj) {
        return new EmpModel($fetchedObj);
      }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function deleteByNo($no) {
      $stmt = DBConnect::stream('prepare',
        DELETE_FROM(TNAME).
        WHERE(IS_MATCH(NO))
      );
      $stmt->bindParam(NO, $no, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }

    public static function updateToNo($no, $dataset) {
      $cols = array_keys($dataset);
      $stmt = DBConnect::stream('prepare',
        UPDATE_TO_SET(TNAME, $cols).
        WHERE(IS_MATCH(NO))
      );

      if (in_array(PASS, $cols)) {
        $hashedPass = password_hash($dataset[PASS], PASSWORD_BCRYPT, ['cost' => 10]);
        $dataset[PASS] = $hashedPass;
      }

      foreach ($cols as $col) {
        if ($col != NO) $stmt->bindValue($col, $dataset[$col], self::DATA_TYPES[$col]);
      }
      $stmt->bindParam(NO, $no, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }

    public static function create($newData) {
      // 番号は自動採番
      $stmt = DBConnect::stream('query',
        SELECT(MAX_NUM(NO).'+1').
        FROM(TNAME)
      );
      $newData[NO] = $stmt->fetch(PDO::FETCH_COLUMN)[0];

      $required = [NO, PASS, NAME, DEPT, MGRFLG, ENT_YEAR, ENT_MON, ENT_DAY];
      $fulfilled = true;
      foreach ($required as $r) {
        $fulfilled *= array_key_exists($r, $newData);
      }
      if (!$fulfilled) return false;

      $cols = array_keys($newData);
      $stmt = DBConnect::stream('prepare',
        INSERT_VALUES(TNAME, $cols)
      );

      $hashedPass = password_hash($newData[PASS], PASSWORD_BCRYPT, ['cost' => 10]);
      $newData[PASS] = $hashedPass;

      foreach ($cols as $col) {
        $stmt->bindValue($col, $newData[$col], self::DATA_TYPES[$col]);
      }
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }
  }

?>
