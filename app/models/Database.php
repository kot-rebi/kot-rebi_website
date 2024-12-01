<?php

/**
 * データベース接続管理するクラス
 */
class Database
{
  private static $instance = null;

  /**
   * ホスト名
   * 
   * @var string
   */
  private $host = "localhost";

  /**
   * データベース名
   *
   * @var string
   */
  private $dbname = "cms";

  /**
   * ユーザー名
   *
   * @var string
   */
  private $username = "root";

  /**
   * パスワード
   *
   * @var string
   */
  private $password = "";

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
    $this->connect();
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
  private function connect()
  {
    try {
      $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "接続に失敗しました: " . $e->getMessage();
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