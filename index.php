<?php
require 'vendor/autoload.php';

$parsedown = new Parsedown();
echo $parsedown->text('# HelloComposer');

$requestUri = $_SERVER['REQUEST_URI'];

if (preg_match('/^\/pj_homepage\/articles\/(\d+)$/', $requestUri, $matches)){
  // 記事内容を表示
  // 動的な記事IDを取得する
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/PublicArticleController.php';
  $controller = new PublicArticleController();

  $articleId = intval($matches[1]);
  $controller->show($articleId);

} else if (preg_match('/^\/pj_homepage\/admin\/articles\/create/', $requestUri)) {
  // 新規投稿画面の表示
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/CreateArticleController.php';
  $controller = new CreateArticleController();
  $controller->handleRequest();

} else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $requestUri === '/pj_homepage/admin/articles/delete') {
  // 削除処理の実行
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/DeleteArticleController.php';
  $controller = new DeleteArticleController();
  $controller->handleRequest();

}
else if (preg_match('/^\/pj_homepage\/admin\/articles\/edit/', $requestUri)) {
  // 編集処理
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/EditArticleController.php';
  $controller = new EditArticleController();
  $controller->handleRequest();
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri ==='/pj_homepage/admin/articles/update_publish_date') {
  // 公開日時の設定
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/UpdatePublishDateController.php';
  $controller = new UpdatePublishDateController();
  $controller->handleRequest();
}
else if (preg_match('/^\/pj_homepage\/admin\/articles/', $requestUri)) {
  // 記事一覧の表示
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/ArticleController.php';
  $controller = new ArticleController();
  $controller->listArticles();

}
else {
  // ホームページ
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/PublicArticleListController.php';
  $controller = new PublicArticleListController();
  $controller->listArticles();
}