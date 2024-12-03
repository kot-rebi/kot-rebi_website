<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/Database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/models/ArticleModel.php';

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