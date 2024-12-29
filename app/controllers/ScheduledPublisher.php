<?php
session_start();

require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . 'Database.php';
require MODELS_PATH . 'ArticleModel.php';

class ScheduledPublisher
{
  private $articleModel;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
    date_default_timezone_set('Asia/Tokyo');
  }

  public function publishScheduledArticles()
  {
    $currentDateTime = date('Y-m-d H:i:s');
    echo date_default_timezone_get();
    echo "現在時刻: " . $currentDateTime . "\n";
    $articles = $this->articleModel->getScheduleArticlesForPublishing($currentDateTime);

    foreach ($articles as $article) {
      $result = $this->articleModel->publishArticle($article['id']);
      if ($article['scheduled_publish_date'] === NULL) {
        continue;
      }

      if ($result) {
        echo "記事ID {$article['id']} を公開しました\n";
      } else {
        echo "記事ID {$article['id']} の公開に失敗しました\n";
      }
    }
  }
}

$scheduler = new ScheduledPublisher();
$scheduler->publishScheduledArticles();