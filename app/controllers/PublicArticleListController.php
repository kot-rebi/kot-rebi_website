<?php
require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . 'Database.php';
require MODELS_PATH .'ArticleModel.php';

class PublicArticleListController
{
  private $articleModel;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  public function listArticles()
  {
    // 現在のページをURLパラメータから取得
    if (isset($_GET['page'])) {
      $currentPage = (int)$_GET['page'];
    } else {
      $currentPage = 1;
    }

    // 1ページ当たりの表示件数
    $limit = 10;
    // データ取得の開始位置
    $offset = ($currentPage - 1) * $limit;

    // 記事の取得
    $articles = $this->articleModel->getPublishedArticles($limit, $offset);

    // 総ページ数の取得
    $totalArticles = $this->articleModel->getTotalPublishedArticles();
    $totalPages = ceil($totalArticles / $limit);

    // カテゴリーの取得
    $categories = $this->listCategories();

    require_once VIEWS_HOME_PATH . 'index.php';
  }

  public function listCategories() {
    return $this->articleModel->getCategoriesList();
  }
}
