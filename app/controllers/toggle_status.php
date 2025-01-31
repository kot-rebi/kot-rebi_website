<?php
require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . '/Database.php';
require MODELS_PATH .'/ArticleModel.php';

if (isset($_GET['id'])) {
  $articleId = $_GET['id'];

  $articleModel = new ArticleModel(Database::getInstance());
  $result = $articleModel->togglePublish($articleId);

  if ($result) {
    echo "公開ステータスが変更されました";
  } else {
    echo "公開ステータスの変更に失敗しました";
  }
} else {
  echo "記事IDが指定されていません";
}