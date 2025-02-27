<?php


class PublicArticleListController
{
  private $config;
  private $articleModel;

  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  public function listArticles()
  {
    // URLパラメータから取得
    // 現在のページ
    if (isset($_GET['page'])) {
      $currentPage = (int)$_GET['page'];
    } else {
      $currentPage = 1;
    }

    // カテゴリID
    if (isset($_GET['category'])) {
      $categoryId = (int)$_GET['category'];
    } else {
      $categoryId = null;
    }

    // 1ページ当たりの表示件数
    $limit = 10;
    // データ取得の開始位置
    $offset = ($currentPage - 1) * $limit;

    // 記事の取得（カテゴリが指定されている場合はフィルターをして取得）
    if ($categoryId) {
      $articles = $this->articleModel->getArticlesByCategory($categoryId, $limit, $offset);
      $totalArticles = $this->articleModel->getTotalArticlesByCategory($categoryId);
    } else {
      $articles = $this->articleModel->getPublishedArticles($limit, $offset);
      $totalArticles = $this->articleModel->getTotalPublishedArticles();
    }

    // 記事の取得
    // $articles = $this->articleModel->getPublishedArticles($limit, $offset);

    // 総ページ数の取得
    $totalArticles = $this->articleModel->getTotalPublishedArticles();
    $totalPages = ceil($totalArticles / $limit);

    // カテゴリーの取得
    $categories = $this->listCategories();

    // ゲームの取得
    $games = $this->articleModel->getGames();

    require_once $this->config->get('paths')['views_home'] . '/index.php';
  }

  public function listCategories()
  {
    return $this->articleModel->getCategoriesList();
  }
}
