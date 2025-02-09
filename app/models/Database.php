<?php

/**
 * データベース接続管理するクラス
 */
class Database
{
  private static $instance = null;

  /**
   * PDO接続オブジェクト
   *
   * @var PDO|null
   */
  private $connection;

  /**
   * コンストラクタ
   * データベース接続を初期化する
   */
  public function __construct()
  {
    $config = Config::getInstance();
    $dbConfig = $config->get('db');

    $this->connect($dbConfig);
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  /**
   * データベースへの接続を確立する
   *
   * @return void
   * @throws PDOException 接続に失敗
   */
  private function connect($dbConfig)
  {
    try {
      $this->connection = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", $dbConfig['username'], $dbConfig['password']);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {

      die("接続に失敗しました: " . $e->getMessage());
    }
  }


  /**
   * データベース接続を取得
   *
   * @return PDO
   */
  public function getConnection()
  {
    return $this->connection;
  }

  /**
   * データベース接続を閉じる
   *
   * @return void
   */
  public function close()
  {
    $this->connection = null;
  }

  public function __clone() {}
  public function __wakeup() {}
}
