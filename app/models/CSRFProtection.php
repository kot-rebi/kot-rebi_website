<?php

class CSRFProtection
{
  private $tokenKey = 'csrf_token';

  public function __construct() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * トークンを生成してセッションに保存する
   */
  public function generateToken()
  {
    if (empty($_SESSION[$this->tokenKey])) {
      $_SESSION[$this->tokenKey] = bin2hex(random_bytes(32));
    }
  }

  /**
   * トークンを埋め込む
   */
  public function getToken() {
    return $_SESSION[$this->tokenKey] ?? null;
  }

  /**
   * トークンを削除する
   */
  public function destroyToken() {
    unset($_SESSION[$this->tokenKey]);
  }
}