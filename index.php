<?php
require_once 'bootstrap.php';
require_once 'auth.php';

$config = Config::getInstance();

require_once $config->get('paths')['models'] . '/UserModel.php';
require_once $config->get('paths')['controllers'] . '/LoginController.php';

$userModel = new UserModel();
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];           // クエリパラメーターを省いたURL
$matched = false; // ルートが見つかったかのフラグ

// 記事内容の表示（公開ページなのでログインチェックは不要）
if (preg_match('/^' . preg_quote($config->get('urls')['articles'], '/') . '\/(\d+)$/', $requestUri, $matches)) {
  // 動的な記事IDを取得する
  require_once $config->get('paths')['controllers'] . '/PublicArticleController.php';
  $controller = new PublicArticleController();
  $articleId = intval($matches[1]);
  $controller->show($articleId);
  $matched = true;
} 

// 管理画面（ログインチェックを行なう）
// 新規投稿画面
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_create'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers'] . '/CreateArticleController.php';
  $controller = new CreateArticleController();
  $controller->handleRequest();
  $matched = true;
} 
// 削除画面
else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $requestUri === $config->get('urls')['admin_articles_delete']) {
  require_once $config->get('paths')['controllers'] . '/DeleteArticleController.php';
  $controller = new DeleteArticleController();
  $controller->handleRequest();
  $matched = true;
} 
// 編集画面
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_edit'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers'] . '/EditArticleController.php';
  $controller = new EditArticleController();
  $controller->handleRequest();
  $matched = true;
} 
// 公開日時の設定
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri === $config->get('urls')['admin_articles_publish_date']) {
  requireLogin();
  require_once $config->get('paths')['controllers'] . '/UpdatePublishDateController.php';
  $controller = new UpdatePublishDateController();
  $controller->handleRequest();
  $matched = true;
} 
// 記事一覧の表示
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers'] . '/ArticleController.php';
  $controller = new ArticleController();
  $controller->listArticles();
  $matched = true;
}

// トップページ（公開ページ）
else if ($path == $config->get('BASE_URL') . '/' || $path === $config->get('BASE_URL')) {
  require_once $config->get('paths')['controllers'] . '/PublicArticleListController.php';
  $controller = new PublicArticleListController();
  $controller->listArticles();
  $matched = true;
}
// ログインページ
else if ($path === $config->get('urls')['admin_login']) {
  $controller = new LoginController($userModel);
  $controller->login();
  $matched = true;
}
// ログアウトページ
else if ($path === $config->get('urls')['admin_logout']) {
  require_once $config->get('paths')['controllers'] . '/LogoutController.php';
  $controller = new LogoutController();
  $controller->logout();
  $matched = true;
}

// 404エラーページ
if (!$matched) {
  http_response_code(404);
  require_once $config->get('paths')['views_home'] . '/error-404.php';
}
