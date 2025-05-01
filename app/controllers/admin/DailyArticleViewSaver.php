<?php

require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../models/Database.php';
require_once __DIR__ . '/../../models/ArticleModel.php';
require_once __DIR__ . '/../../models/GoogleAnalyticsModel.php';


class DailyArticleViewSaver
{
  private $config;
  private $articleModel;
  private $gaModel;

  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->articleModel = new ArticleModel(Database::getInstance());
    $this->gaModel = new GoogleAnalyticsModel();
    date_default_timezone_set('Asia/Tokyo');
  }

  public function saveViewsDaily()
  {
    $startDate = date('Y-m-d', strtotime('-1 day'));
    $endDate = $startDate;

    $articleViews = $this->gaModel->getMostViewedArticlesWithViews($startDate, $endDate, 50);

    if (empty($articleViews)) {
      echo "保存対象のPVデータが存在しません";
      return;
    }

    foreach ($articleViews as $articleId => $views) {
      // 記事が存在するか確認（外部キー制約回避用）
      if (!$this->articleModel->articleExists($articleId)) {
        echo "記事ID {$articleId} は存在しないためスキップ\n";
        continue;
      }

      $this->articleModel->saveDailyViews($articleId, $views, $startDate);
      echo "記事ID: {$articleId} のPV: {$views} を保存しました\n";
    }
  }

  public function updatePastViews($startDate, $endDate, $limit) 
  {
    $articleViews = $this->gaModel->getMostViewedArticlesWithViews($startDate, $endDate, $limit);

    if (empty($articleViews)) {
      echo "保存対象のPVデータが存在しません\n";
      return;
    }

    foreach($articleViews as $articleId => $views) {
      // 記事が存在するか確認
      if (!$this->articleModel->articleExists($articleId)) {
        echo "記事ID {$articleId} は存在しないためスキップ\n";
        continue;
      }

      $this->articleModel->saveDailyViews($articleId, $views, $startDate);
      echo "記事ID: {$articleId} のPV: {$views} を更新しました\n";
    }
  }
}

// 実行部分


ob_start();

echo "\n=====================================\n";
echo "PV保存処理: " . date('Y-m-d H:i:s') . "\n";
echo "=====================================\n";

$saver = new DailyArticleViewSaver();
$saver->saveViewsDaily();

// ログの保存
$logContent = ob_get_clean();
file_put_contents(__DIR__ . '/../scheduler.log', $logContent . "\n", FILE_APPEND);

// エラーログの設定
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');
