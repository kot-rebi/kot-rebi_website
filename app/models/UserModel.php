<?php
class UserModel {
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function getUserByUsername($username) {
    try { 
      $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
      $stmt->bindValue('username', $username, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch();
    } catch (PDOException $e) {
      echo "取得に失敗しました: " . $e->getMessage();
      return [];
    }
  }
}