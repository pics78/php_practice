<?php

  require_once __DIR__.'/../utils/loadEnv.php';
  require_once __DIR__.'/dbConnectException.php';

  class DBConnect {
    private static $dbh = null;
    private function __construct() {}

    public static function init() {
      try {
        self::$dbh = new PDO(
          // data source name
          'mysql:host='.$_ENV['MYSQL_HOST'].'; dbname='.$_ENV['MYSQL_NAME'].'; charset=utf8',
          // user
          $_ENV['MYSQL_USER'],
          // password
          $_ENV['MYSQL_PASS']
        );
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        // エラー処理
        throw new DBConnectException($e);
      }
    }

    public static function close() {
      self::$dbh = null;
    }

    public static function stream($pdoMethod, ...$args) {
      if (self::$dbh == null) self::init();

      if (method_exists(self::$dbh, $pdoMethod)) {
        return call_user_func_array(array(self::$dbh, $pdoMethod), $args);
      }
      // method is not exists.
      return null;
    }
  }

?>
