<?php

require_once 'bootstrap.php';

$config = Config::getInstance();

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];           // クエリパラメーターを省いたURL
$matched = false; // ルートが見つかったかのフラグ

if (preg_match('/^' . preg_quote($config->get('urls')['articles'], '/') . '\/(\d+)$/', $requestUri, $matches)) {
  // 記事内容を表示
  // 動的な記事IDを取得する
  require_once $config->get('paths')['controllers'] . '/PublicArticleController.php';
  $controller = new PublicArticleController();

  $articleId = intval($matches[1]);
  $controller->show($articleId);
  $matched = true;
} else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_create'], '/') . '\/?/', $requestUri)) {
  // 新規投稿画面の表示
  require_once $config->get('paths')['controllers'] . '/CreateArticleController.php';
  $controller = new CreateArticleController();
  $controller->handleRequest();
  $matched = true;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $requestUri === $config->get('urls')['admin_articles_delete']) {
  // 削除処理の実行
  require_once $config->get('paths')['controllers'] . '/DeleteArticleController.php';
  $controller = new DeleteArticleController();
  $controller->handleRequest();
  $matched = true;
} else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_edit'], '/') . '\/?/', $requestUri)) {
  // 編集処理
  require_once $config->get('paths')['controllers'] . '/EditArticleController.php';
  $controller = new EditArticleController();
  $controller->handleRequest();
  $matched = true;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri === $config->get('urls')['admin_articles_publish_date']) {
  // 公開日時の設定
  require_once $config->get('paths')['controllers'] . '/UpdatePublishDateController.php';
  $controller = new UpdatePublishDateController();
  $controller->handleRequest();
  $matched = true;
} else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles'], '/') . '\/?/', $requestUri)) {
  // 記事一覧の表示
  require_once $config->get('paths')['controllers'] . '/ArticleController.php';
  $controller = new ArticleController();
  $controller->listArticles();
  $matched = true;
} else if ($path == $config->get('BASE_URL') . '/' || $path === $config->get('BASE_URL')) {
  require_once $config->get('paths')['controllers'] . '/PublicArticleListController.php';
  $controller = new PublicArticleListController();
  $controller->listArticles();
  $matched = true;
}

// どのルートにもマッチしなかったら404ページを表示
if (!$matched) {
  http_response_code(404);
  require_once $config->get('paths')['views_home'] . '/error-404.php';
}
