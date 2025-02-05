<?php
session_start();

class UpdatePublishDateController
{
  private $articleModel;
  private $config;

  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $articleId = $_POST['article_id'] ?? null;
      $scheduledPublishDate = $_POST['scheduled_publish_date'] ?? null;

      // 入力データの確認
      if (!$articleId || !$scheduledPublishDate) {
        $_SESSION['error_message'] = '記事IDまたは公開日時が無効です';
        echo '記事IDまたは公開日時が無効です';
        header('Location: ' . $this->config->get('urls')['admin_articles']);
        exit;
      }

      $articleModel = new ArticleModel(Database::getInstance());
      $result = $articleModel->updatePublishDate($articleId, $scheduledPublishDate);

      if ($result) {
        $_SESSION['success_message'] = '公開日時が設定されました！';
        echo '公開日時が設定されました！';
      } else {
        $_SESSION['error_message'] = '公開日時の設定に失敗しました';
        echo '公開日時の設定に失敗しました';
      }
    }

    header('Location: ' . $this->config->get('urls')['admin_articles']);
    exit;
  }
}
