<?php

class PublicArticleController
{
  private $config;
  private $articleModel;
  private $parsedown;

  public function __construct()
  {

    $this->config = Config::getInstance();
    require $this->config->get('paths')['libs'] . '/CustomParsedown.php';
    $this->articleModel = new ArticleModel(Database::getInstance());
    $this->parsedown = new CustomParsedown();
    $this->parsedown->setSafeMode(false);
  }

  public function show($categorySlug, $articleSlug, $articleId)
  {
    // 記事IDが指定されていない場合や不正な場合
    if (!$articleId) {
      http_response_code(404);
      include $config->get('paths')['views'] . '/error-404.php';
      return;
    }

    // 最初に記事IDで取得
    $article = $this->articleModel->getArticleById($articleId);
    
    // 記事IDで見つからなかった場合、カテゴリーと記事スラッグで検索
    if (!$article) {
      $article = $this->articleModel->getArticleByCategorySlugAndArticleSlug($categorySlug, $articleSlug);
    }
    
    // 記事が見つからないとき
    if (!$article) {
      http_response_code(404);
      // エラーページ404に飛ぶ
      include $config->get('paths')['libs'] . '/error-404.php';
      // echo "記事が見つかりません";
      return;
    }

    // パンくずリスト用：この1記事が所属するカテゴリーを取得
    $category = $this->articleModel->getCategoryById($article['category_id']);

    // サイドバー：人気記事の取得
    $popularArticles = $this->articleModel->getPopularArticles(3);

    // サイドバー：カテゴリーの取得
    $categories = $this->listCategories();

    // マークダウン変換など表示準備
    $article['content_html'] = $this->parsedown->text($article['content']);

    include $this->config->get('paths')['views_home'] . '/article.php';
  }
  public function listCategories()
  {
    return $this->articleModel->getCategoriesList();
  }
}
