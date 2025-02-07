<?php

define('ROOT_PATH', dirname(__DIR__, 2));

function getUrl($path) {
  $baseUrl = ($_SERVER['HTTP_HOST'] === 'localhost') ? '' : '';
  return $baseUrl . $path;
}

return [
  // ルートディレクトリ
  // コントローラーやモデルのパスはROOT_PATHを基準に定数化する
  'ROOT_PATH' => ROOT_PATH,
  'APP_PATH' => ROOT_PATH . '/app',
  'FUNCTIONS_PATH' => ROOT_PATH . '/app/functions.php',
  
  // MVC構造のパス
  'paths' => [
    'controllers' => ROOT_PATH . '/app/controllers',
    'controllers_admin' => ROOT_PATH . '/app/controllers/admin',
    'libs' => ROOT_PATH . '/app/libs',
    'models' => ROOT_PATH . '/app/models',
    'views' => ROOT_PATH . '/app/views',
    'views_admin' => ROOT_PATH . '/app/views/admin',
    'global_shared' => ROOT_PATH . '/app/views/shared',
    'views_home' => ROOT_PATH . '/app/views/home',
    'views_home_shared' => ROOT_PATH . '/app/views/home/shared',
    'core' => ROOT_PATH . '/app/core',
    'config' => ROOT_PATH . '/app/config',
    'css' => getUrl('/assets/css'),
    'css_admin' => getUrl('/assets/css/admin'),
    'js_admin' => getUrl('/assets/js/admin'),
  ],

  // アセット関連のパス
  'assets' => [
    'image' => getUrl('/assets/image'),
    'thumbnails' => ROOT_PATH . '/assets/image/uploads/thumbnails',
    'articles' => ROOT_PATH . '/assets/image/uploads/articles',
  ],

  // ルーティング
  'BASE_URL' => ($_SERVER['HTTP_HOST'] === 'localhost') ? '' : '',
  'urls' => [
    'admin_login' => getUrl('/admin/login'),
    'admin_logout' => getUrl('/admin/logout'),
    'admin_articles' => getUrl('/admin/articles'),
    'admin_articles_create' => getUrl('/admin/articles/create'),
    'admin_articles_delete' => getUrl('/admin/articles/delete'),
    'admin_articles_edit' => getUrl('/admin/articles/edit'),
    'admin_articles_publish_date' => getUrl('/admin/articles/publish_date'),
    'articles' => getUrl('/articles'),
    'app' => getUrl('/app'),
    'controllers' => getUrl('/app/controllers'),
  ],

  // データベース設定
  'db' => [
    'host' => 'localhost',
    'dbname' => 'cms',
    'username' => 'root',
    'password' => '',
  ],

  // テーブル名
  'tables' => [
    'articles' => 'test_articles',
    'thumbnails' => 'test_article_thumbnails',
    'images' => 'test_article_images',
    'categories' => 'test_article_categories',
  ],

];