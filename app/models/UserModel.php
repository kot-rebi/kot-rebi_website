<?php

class UserModel {
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  public function getUserByUsername($username) {
    try { 
      $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
      $stmt->bindValue('username', $username, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch();
    } catch (PDOException $e) {
      echo "取得に失敗しました: " . $e->getMessage();
      return [];
    }
  }
}