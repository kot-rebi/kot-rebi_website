<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/Database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/ArticleModel.php';

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

    require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/home/index.php';
  }
}
