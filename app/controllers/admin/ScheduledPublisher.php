<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/ArticleModel.php';

/**
 * スケジュールが来たら記事を公開にするクラス
 * XserverのCronジョブで使うことを想定
 */
class ScheduledPublisher
{
  private $config;
  private $articleModel;

  /**
   * コンストラクタ
   */
  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->articleModel = new ArticleModel(Database::getInstance());
    date_default_timezone_set('Asia/Tokyo');
  }

  /**
   * スケジュール時間が来たら記事の公開ステータスを変える
   */
  public function publishScheduledArticles()
  {
    $currentDateTime = date('Y-m-d H:i:s');
    echo "現在時刻: " . $currentDateTime . "\n";

    $articles = $this->articleModel->getScheduleArticlesForPublishing($currentDateTime);

    if (empty($articles)) {
      echo "公開予定の記事はありません \n";
      return;
    }

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

// 実行部分
$scheduler = new ScheduledPublisher();

ob_start();

echo "\n=====================================\n";
echo "実行日時: " . date('Y-m-d H:i:s') . "\n";
echo "=====================================\n";

$scheduler->publishScheduledArticles();
$logContent = ob_get_clean();

// ログの保存
file_put_contents(__DIR__ . '/../scheduler.log', $logContent . "\n", FILE_APPEND);

// エラーログの設定
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');
