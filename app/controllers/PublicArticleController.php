<?php
require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . 'Database.php';
require MODELS_PATH .'ArticleModel.php';
require LIBS_PATH . 'CustomParsedown.php';
require 'vendor/autoload.php';

class PublicArticleController 
{
  private $articleModel;
  private $parsedown;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
    $this->parsedown = new CustomParsedown();
    $this->parsedown->setSafeMode(true);
  }

  public function show($articleId)
  {

    // 記事IDが指定されていない場合や不正な場合
    if (!$articleId) {
      http_response_code(400);
      echo "不正なリクエストです";
      return;
    }

    // 記事の取得
    $article = $this->articleModel->getArticleById($articleId);

    // 記事が見つからないとき
    if (!$article)
    {
      http_response_code(404);
      // TODO: エラーページ404を作成し、飛ぶ処理
      echo "記事が見つかりません";
      return;
    }

    $article['content_html'] = $this->parsedown->text($article['content']);

    include VIEWS_HOME_PATH . 'article.php';
  }
}