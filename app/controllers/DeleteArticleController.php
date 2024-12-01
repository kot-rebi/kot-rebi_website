<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/Database.php';
require $_SERVER['DOCUMENT_ROOT'] .'/pj_homepage/app/models/ArticleModel.php';

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
      header("Location: /pj_homepage/admin/articles");
      exit;
    } else {
      $_SESSION['error_message'] = "無効な記事IDです";
      header("Location: /pj_homepage/admin/articles");
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