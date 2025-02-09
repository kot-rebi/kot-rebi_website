<?php

class LogoutController {
  private $config;

  public function __construct()
  {
    $this->config = Config::getInstance();
  }

  /**
   * ログアウト処理
   *
   * @return void
   */
  public function logout()
  {
    session_start();
    session_unset();
    session_destroy();
    header("Location: " . $this->config->get('urls')['admin_login']);
    exit;
  }
}