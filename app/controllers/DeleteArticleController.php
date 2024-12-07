<?php
session_start();

require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . 'Database.php';
require MODELS_PATH . 'ArticleModel.php';

class DeleteArticleController
{
  private $articleModel;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  public function handleRequest()
  {
    $id = $this->getArticleID();
    if ($this->validate($id))
    {
      $this->articleModel->deleteArticles($id);
      header("Location: ") . ADMIN_ARTICLES_URL;
      exit;
    } else {
      $_SESSION['error_message'] = "無効な記事IDです";
      header("Location: " . ADMIN_ARTICLES_URL);
    }
  }

  public function getArticleID()
  {
    return $_POST['article_id'] ?? null;
  }

  private function validate($id)
  {
    if (!isset($id) || !is_numeric($id))
    {
      return false;
    }
    return true;
  }
}