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

    require_once $this->config->get('paths')['models'] . '/CSRFProtection.php';
    $csrf = new CSRFProtection();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      // CSRFトークン検証
      if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $csrf->getToken()) {
        die("CSRF検証に失敗しました");
      }

      $username = $_POST["username"];
      $password = $_POST["password"];
      $user = $this->userModel->getUserByUsername($username);

      // パスワードの照合
      if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $csrf->destroyToken();
        header("Location: " . $this->config->get('urls')['admin_articles']);
        exit;
      } else {
        $_SESSION["error"] = "ユーザー名またはパスワードが違います";
      }
    }
    require_once $this->config->get('paths')['views_admin'] . "/Login.php";
  }
}