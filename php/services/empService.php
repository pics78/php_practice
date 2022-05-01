<?php

  require_once __DIR__.'/../connect/dbConnect.php';
  require_once __DIR__.'/../models/empModel.php';
  require_once __DIR__.'/../models/evalModel.php';
  require_once __DIR__.'/../const/sqlConst.php';
  require_once __DIR__.'/../utils/sqlUtil.php';

  class EmpService {

    const DATA_TYPES = array(
      /* 社員基本情報 */
      NO        => PDO::PARAM_INT,
      PASS      => PDO::PARAM_STR,
      NAME      => PDO::PARAM_STR,
      DEPT_ID   => PDO::PARAM_INT,
      DEPT_NAME => PDO::PARAM_STR,
      AUTHFLG   => PDO::PARAM_INT,
      AUTH_NAME => PDO::PARAM_STR,
      ENT_YEAR  => PDO::PARAM_INT,
      ENT_MON   => PDO::PARAM_INT,
      ENT_DAY   => PDO::PARAM_INT,
      LEV_YEAR  => PDO::PARAM_INT,
      LEV_MON   => PDO::PARAM_INT,
      LEV_DAY   => PDO::PARAM_INT,
      ADDRESS   => PDO::PARAM_STR,
      /* 社員評価情報 */
      EVAL_YEAR       => PDO::PARAM_INT,
      GOAL            => PDO::PARAM_STR,
      FST_RES         => PDO::PARAM_STR,
      SCD_RES         => PDO::PARAM_STR,
      TOTAL_RES       => PDO::PARAM_STR,
      FST_EVAL        => PDO::PARAM_STR,
      FST_EVALUATOR   => PDO::PARAM_INT,
      SCD_EVAL        => PDO::PARAM_STR,
      SCD_EVALUATOR   => PDO::PARAM_INT,
      TOTAL_EVAL      => PDO::PARAM_STR,
      TOTAL_EVALUATOR => PDO::PARAM_INT
    );

    private static function doHash($pass) {
      return password_hash($pass, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function getDeptList() {
      $stmt = DBConnect::stream('query',
        SELECT(DEPT_ID, DEPT_NAME).
        FROM(DEPT_MST).
        ORDER_BY(DEPT_ID, ASC)
      );
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAuthList() {
      $stmt = DBConnect::stream('query',
        SELECT(AUTH_ID, AUTH_NAME).
        FROM(AUTH_MST).
        ORDER_BY(AUTH_ID, ASC)
      );
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function selectByNo($no, ...$items) {
      $joinedDeptFlg = in_array(DEPT_NAME, $items);
      $joinedAuthFlg = in_array(AUTH_NAME, $items);
      $stmt = DBConnect::stream('prepare',
        SELECT_LIST($items).
        FROM(TNAME,
          $joinedDeptFlg ? innerJoin(DEPT_MST, DEPT_ID, DEPT_ID) : null,
          $joinedAuthFlg ? innerJoin(AUTH_MST, AUTHFLG, AUTH_ID) : null).
        WHERE(IS_MATCH(NO))
      );
      $stmt->bindParam(NO, $no, PDO::PARAM_INT);
      $stmt->execute();
      if ($stmt->rowCount() == 1) {
        return new EmpModel($stmt->fetch(PDO::FETCH_ASSOC));
      }
      return null;
    }

    public static function getAll(...$items) {
      $joinedDeptFlg = in_array(DEPT_NAME, $items);
      $joinedAuthFlg = in_array(AUTH_NAME, $items);
      $stmt = DBConnect::stream('query',
        SELECT_LIST($items).
        FROM(TNAME,
          $joinedDeptFlg ? innerJoin(DEPT_MST, DEPT_ID, DEPT_ID) : null,
          $joinedAuthFlg ? innerJoin(AUTH_MST, AUTHFLG, AUTH_ID) : null).
        ORDER_BY(NO, ASC)
      );
      return array_map(function($fetchedObj) {
        return new EmpModel($fetchedObj);
      }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getEvalByNo($no, $yearList, ...$eitems) {
      $stmt = DBConnect::stream('prepare',
        SELECT_LIST($eitems).
        FROM(TNAME, innerJoin(EVAL_INFO, NO, NO)).
        WHERE(IS_MATCH(NO, EVAL_INFO)._AND_.IN_VALUES(EVAL_YEAR, $yearList)).
        ORDER_BY(EVAL_YEAR, ASC)
      );
      $stmt->bindParam(NO, $no, PDO::PARAM_INT);
      $stmt->execute();
      return array_map(function($fetchedObj) {
        return new EvalModel($fetchedObj);
      }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function deleteBy(EmpModel $m) {
      if ($m == null || $m->getProp(NO) == null) return false;

      try {
        DBConnect::beginTransaction();

        // 評価情報削除
        $stmt1 = DBConnect::stream('prepare', DELETE_FROM(EVAL_INFO).WHERE(IS_MATCH(NO)));
        $stmt1->bindParam(NO, $m->getProp(NO), PDO::PARAM_INT);
        $stmt1->execute();
        // 詳細情報削除
        $stmt2 = DBConnect::stream('prepare', DELETE_FROM(TNAME).WHERE(IS_MATCH(NO)));
        $stmt2->bindParam(NO, $m->getProp(NO), PDO::PARAM_INT);
        $stmt2->execute();

        return $stmt2->rowCount() == 1 ? DBConnect::commit() : false;

      } catch (PDOException $e) {
        DBConnect::rollBack();
        throw $e;
      }
    }

    public static function update(EmpModel $m) {
      if ($m == null || $m->getProp(NO) == null) return false;
      
      $keys = $m->propKeys();
      $stmt = DBConnect::stream('prepare',
        UPDATE_TO_SET(TNAME, $keys).
        WHERE(IS_MATCH(NO))
      );

      if (in_array(PASS, $keys)) {
        // パスワードはハッシュ化して格納し直す
        $hashedPass = self::doHash($m->getProp(PASS));
        $m->setProp(PASS, $hashedPass);
      }

      foreach ($keys as $key) $stmt->bindValue($key, $m->getProp($key), self::DATA_TYPES[$key]);
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }

    public static function updateEval(EvalModel $m) {
      if ($m == null || $m->getProp(NO) == null || $m->getProp(EVAL_YEAR) == null) return false;

      $keys = $m->propKeys();
      $stmt = DBConnect::stream('prepare',
        UPDATE_TO_SET(EVAL_INFO, $keys).
        WHERE(IS_MATCH(NO)._AND_.IS_MATCH(EVAL_YEAR))
      );

      foreach ($keys as $key) $stmt->bindValue($key, $m->getProp($key), self::DATA_TYPES[$key]);
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }

    public static function create(EmpModel $m) {
      $keys = $m->propKeys();

      // 社員番号は自動採番
      $stmt = DBConnect::stream('query',
        SELECT(MAX_NUM(NO).'+1').
        FROM(TNAME)
      );
      $no = $stmt->fetchColumn();
      $m->setProp(NO, $no);

      $stmt = DBConnect::stream('prepare',
        INSERT_VALUES(TNAME, $keys)
      );

      $hashedPass = self::doHash($m->getProp(PASS));
      $m->setProp(PASS, $hashedPass);

      foreach ($keys as $key) {
        $stmt->bindValue($key, $m->getProp($key), self::DATA_TYPES[$key]);
      }
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }

    public static function createEval($no, $year) {
      $stmt = DBConnect::stream('prepare',
        INSERT_VALUES(EVAL_INFO, [NO, EVAL_YEAR])
      );
      $stmt->bindParam(NO, $no, self::DATA_TYPES[NO]);
      $stmt->bindParam(EVAL_YEAR, $year, self::DATA_TYPES[EVAL_YEAR]);
      $stmt->execute();

      return $stmt->rowCount() == 1 ? true : false;
    }
  }

?>
