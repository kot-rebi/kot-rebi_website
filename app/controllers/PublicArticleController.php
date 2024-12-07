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

  public function show()
  {
    // TODO: IDは仮（記事選択画面が完成したら動的に取得
    $articleId = 1;
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