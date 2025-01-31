<?php
require 'vendor/autoload.php';
require_once 'config.php';

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];           // クエリパラメーターを省いたURL
$matched = false; // ルートが見つかったかのフラグ

if (preg_match('/^' . preg_quote(ARTICLES_URL, '/') . '\/(\d+)$/', $requestUri, $matches)) {
  // 記事内容を表示
  // 動的な記事IDを取得する
  require_once CONTROLLERS_PATH . '/PublicArticleController.php';
  $controller = new PublicArticleController();

  $articleId = intval($matches[1]);
  $controller->show($articleId);
  $matched = true;
} else if (preg_match('/^' . preg_quote(ADMIN_ARTICLES_CREATE_URL, '/') . '\/?/', $requestUri)) {
  // 新規投稿画面の表示
  require_once CONTROLLERS_PATH . '/CreateArticleController.php';
  $controller = new CreateArticleController();
  $controller->handleRequest();
  $matched = true;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $requestUri === ADMIN_ARTICLES_DELETE_URL) {
  // 削除処理の実行
  require_once CONTROLLERS_PATH . '/DeleteArticleController.php';
  $controller = new DeleteArticleController();
  $controller->handleRequest();
  $matched = true;
} else if (preg_match('/^' . preg_quote(ADMIN_ARTICLES_EDIT_URL, '/') . '\/?/', $requestUri)) {
  // 編集処理
  require_once CONTROLLERS_PATH . '/EditArticleController.php';
  $controller = new EditArticleController();
  $controller->handleRequest();
  $matched = true;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri === ADMIN_ARTICLES_PUBLISHDATE_URL) {
  // 公開日時の設定
  require_once CONTROLLERS_PATH . '/UpdatePublishDateController.php';
  $controller = new UpdatePublishDateController();
  $controller->handleRequest();
  $matched = true;
} else if (preg_match('/^' . preg_quote(ADMIN_ARTICLES_URL, '/') . '\/?/', $requestUri)) {
  // 記事一覧の表示
  require_once CONTROLLERS_PATH . '/ArticleController.php';
  $controller = new ArticleController();
  $controller->listArticles();
  $matched = true;
} else if ($path == BASE_URL . '/' || $path === BASE_URL) {
  require_once CONTROLLERS_PATH . '/PublicArticleListController.php';
  $controller = new PublicArticleListController();
  $controller->listArticles();
  $matched = true;
}

// どのルートにもマッチしなかったら404ページを表示
if (!$matched) {
  http_response_code(404);
  require_once VIEWS_HOME_PATH . '/error-404.php';
}
