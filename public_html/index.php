<?php

require_once __DIR__ . '/../app/config/Config.php';
$config = Config::getInstance();

require_once $config->get('paths')['models'] . '/UserModel.php';
require_once $config->get('paths')['controllers_admin'] . '/LoginController.php';
require_once $config->get('paths')['core'] . '/bootstrap.php';
require_once $config->get('paths')['config'] . '/auth.php';

$userModel = new UserModel();
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];           // クエリパラメーターを省いたURL
$matched = false; // ルートが見つかったかのフラグ

if ($path == '/privacy-policy') {
  require_once $config->get('paths')['views_home'] . '/static/privacy-policy.php';
  $matched = true;
}
// カテゴリーページ
else if (preg_match('#^/([^/]+)$#', $path, $matches)) {
  require_once $config->get('paths')['controllers'] . '/PublicArticleListController.php';
  $controller = new PublicArticleListController();
  $controller->listArticlesByCategorySlug($matches[1]);
  $matched = true;
}
// 記事内容の表示（公開ページなのでログインチェックは不要）
else if (preg_match('#^/([^/]+)/([a-zA-Z0-9\-]+)-(\d+)$#', $path, $matches)) {
  // 動的な記事ID、スラッグを取得する
  require_once $config->get('paths')['controllers'] . '/PublicArticleController.php';
  $controller = new PublicArticleController();

  // マッチした値
  $categorySlug = $matches[1];
  $articleSlug = $matches[2];
  $articleId = intval($matches[3]);

  // IDを元に取得して記事固有のページを表示
  $controller->show($categorySlug, $articleSlug, $articleId);
  $matched = true;
}
// 旧URLの形式（/articles/記事ID）にアクセスした時に新URLに飛ばす
else if (preg_match('#^/articles/(\d+)$#', $path, $matches)) {
  $articleId = intval($matches[1]);

  $articleModel = new ArticleModel(Database::getInstance());
  $slugs = $articleModel->getSlugsByArticleId($articleId);

  if ($slugs && !empty($slugs['article_slug']) && !empty($slugs['category_slug'])) {
    $newUrl = '/' . $slugs['category_slug'] . '/' . $slugs['article_slug'] . '-' . $articleId;

    // 恒久的リダイレクト
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $newUrl);
    $matched = true;
    exit;
  }

  // スラッグ取得失敗時は404
  $matched = false;
}
// 管理画面（ログインチェックを行なう）
// 新規投稿画面
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_create'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers_admin'] . '/CreateArticleController.php';
  $controller = new CreateArticleController();
  $controller->handleRequest();
  $matched = true;
}
// 削除画面
else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $requestUri === $config->get('urls')['admin_articles_delete']) {
  require_once $config->get('paths')['controllers_admin'] . '/DeleteArticleController.php';
  $controller = new DeleteArticleController();
  $controller->handleRequest();
  $matched = true;
}
// 編集画面
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_edit'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers_admin'] . '/EditArticleController.php';
  $controller = new EditArticleController();
  $controller->handleRequest();
  $matched = true;
}
// 公開日時の設定
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestUri === $config->get('urls')['admin_articles_publish_date']) {
  requireLogin();
  require_once $config->get('paths')['controllers_admin'] . '/UpdatePublishDateController.php';
  $controller = new UpdatePublishDateController();
  $controller->handleRequest();
  $matched = true;
}
// 記事一覧の表示
else if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles'], '/') . '\/?/', $requestUri)) {
  requireLogin();
  require_once $config->get('paths')['controllers_admin'] . '/ArticleController.php';
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
  require_once $config->get('paths')['controllers_admin'] . '/LogoutController.php';
  $controller = new LogoutController();
  $controller->logout();
  $matched = true;
}

// 404エラーページ
if (!$matched) {
  http_response_code(404);
  require_once $config->get('paths')['views_home'] . '/error-404.php';
}
