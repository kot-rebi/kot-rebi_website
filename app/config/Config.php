<?php

class config
{
  private static $instance = null;
  private $config;

  private function __construct()
  {
    $this->config = require_once __DIR__ . '/config_setting.php';
  }

  public static function getInstance()
  {
    if (self::$instance == null)
    {
      self::$instance = new Config();
    }
    return self::$instance;
  }


  public function get($key)
  {
    return $this->config[$key] ?? null;
  }
}