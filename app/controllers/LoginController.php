<?php
class LoginController {
  private $config;
  private $userModel;

  public function __construct($userModel)
  {
    $this->config = Config::getInstance();
    $this->userModel = $userModel;
  }

  /**
   * ログイン処理
   * ユーザー名・パスワードを受け取り照合する
   * @return void
   */
  public function login() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $username = $_POST["username"];
      $password = $_POST["password"];
      $user = $this->userModel->getUserByUsername($username);

      // パスワードの照合
      if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: " . $this->config->get('urls')['admin_articles']);
        exit;
      } else {
        $_SESSION["error"] = "ユーザー名またはパスワードが違います";
      }
    }
    require_once $this->config->get('paths')['views_admin'] . "/Login.php";
  }
}