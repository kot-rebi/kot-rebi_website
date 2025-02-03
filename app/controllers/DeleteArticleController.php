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
    $id = $this->getArticleID();

    if ($this->validate($id)) {
      if ($this->articleModel->deleteArticleProcess($id)) {
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
