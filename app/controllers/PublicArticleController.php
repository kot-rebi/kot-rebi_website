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
    $this->parsedown->setSafeMode(true);
  }

  public function show($articleId)
  {

    // 記事IDが指定されていない場合や不正な場合
    if (!$articleId) {
      http_response_code(404);
      include $config->get('paths')['views'] . '/error-404.php';
      return;
    }

    // 記事の取得
    $article = $this->articleModel->getArticleById($articleId);
    // カテゴリーの取得
    $categories = $this->listCategories();

    // 記事が見つからないとき
    if (!$article) {
      http_response_code(404);
      // TODO: エラーページ404を作成し、飛ぶ処理
      include $config->get('paths')['libs'] . '/error-404.php';
      // echo "記事が見つかりません";
      return;
    }

    $article['content_html'] = $this->parsedown->text($article['content']);

    include $this->config->get('paths')['views_home'] . '/article.php';
  }
  public function listCategories()
  {
    return $this->articleModel->getCategoriesList();
  }
}
