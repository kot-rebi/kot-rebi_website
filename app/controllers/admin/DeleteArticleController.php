<?php
session_start();

class DeleteArticleController
{
  private $articleModel;
  private $config;

  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  public function handleRequest()
  {
    require_once $this->config->get('paths')['models'] . '/CSRFProtection.php';
    $csrf = new CSRFProtection(); 

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $csrf->getToken()) {
        die("CSRF検証に失敗しました");
      }
    }

    $id = $this->getArticleID();

    if ($this->validate($id)) {
      if ($this->articleModel->deleteArticleProcess($id)) {
        $csrf->destroyToken();
        // 削除成功
        header("Location: ") . $this->config->get('urls')['admin_articles'];
        exit;
      } else {
        // 削除失敗
        $_SESSION['error_message'] = "記事の削除に失敗しました";
        header("Location: ") . $this->config->get('urls')['admin_articles'];
        exit;
      }
    } else {
      $_SESSION['error_message'] = "無効な記事IDです";
      header("Location: " . $this->config->get('urls')['admin_articles']);
      exit;
    }
  }

  public function getArticleID()
  {
    return $_POST['article_id'] ?? null;
  }

  private function validate($id)
  {
    if (!isset($id) || !is_numeric($id)) {
      return false;
    }
    return true;
  }
}
