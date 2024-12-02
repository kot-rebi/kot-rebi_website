<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/Database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/ArticleModel.php';
require 'vendor/autoload.php';

class PublicArticleController 
{
  private $articleModel;
  private $parsedown;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
    $this->parsedown = new Parsedown();
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

    include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/home/article.php';
  }
}