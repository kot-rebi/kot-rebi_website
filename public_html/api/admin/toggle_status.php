<?php
require_once '../../../app/core/bootstrap.php';

header('Content-Type: application/json');

// GETパラメーターのチェック
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  http_response_code(400);
  echo json_encode(["success" => false, "error" => "Invalid ID"]);
  exit;
}

$articleId = (int)$_GET['id'];
$articleModel = new ArticleModel(Database::getInstance());
$result = $articleModel->togglePublish($articleId);

if ($result) {
  echo json_encode(["success" => true, "message" => "公開ステータスが変更されました"]);
} else {
  http_response_code(500);
  echo json_encode(["success" => false, "error" => "公開ステータスの変更に失敗しました"]);
}
exit;