<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/Database.php';
require $_SERVER['DOCUMENT_ROOT'] .'/pj_homepage/app/models/ArticleModel.php';
$headerTitleText = "管理画面";

/**
 * 記事に関する操作を管理するコントローラークラス
 */
class ArticleController
{

  private $articleModel;

  public function __construct()
  {
    // $database = new Database();
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  /**
   * 記事一覧を表示する
   *
   * @return void
   */
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
    $articles = $this->articleModel->getArticles($limit, $offset);

    // 総ページ数の取得
    $totalArticles = $this->articleModel->getTotalArticles();
    $totalPages = ceil($totalArticles / $limit);

    include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/admin/ArticleList.php';
  }
}