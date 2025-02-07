<?php
session_start();


/**
 * ユーザーがログインしているか確認する
 *
 * @return boolean ログイン状態かどうか（true: ログイン済み、false: 未ログイン）
 */
function isLoggedIn() {
  return isset($_SESSION["user_id"]);
}

/**
 * ログインしていなければログインページへリダイレクトする
 *
 * @return void
 */
function requireLogin() {
  if (!isLoggedIn()) {
    $config = Config::getInstance();
    header("Location: " . $config->get('urls')['admin_login']);
    exit;
  }
}