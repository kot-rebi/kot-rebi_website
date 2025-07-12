<?php
$config = Config::getInstance();
$requestUri = $_SERVER['REQUEST_URI'];
$ogType = ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' ? 'website' : 'article');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (preg_match('/^\/games\/LastLineDefenseGame\/$/', $requestUri)) {
  $pageTitle = "最終ライン防衛戦線 | ことれいのもり";
  $description = "最終ライン防衛戦線 迫り来る敵を配置してむかえうて！無料で遊べる簡単タワーディフェンスゲーム";
} else if (isset($article['title'])) {
  $pageTitle = htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') . ' | ことれいのもり';
  $description = isset($article) && isset($article['meta_tag']) && $article['meta_tag'] !== null ? $article['meta_tag'] : 'ことれいのもりは技術ブログです。制作で学んだ内容を記事としてまとめ、更新します。完成したゲームも無料で公開しています。';
} else {
  $pageTitle = "ことれいのもり";
  $description = 'ことれいのもりは技術ブログです。制作で学んだ内容を記事としてまとめ、更新します。完成したゲームも無料で公開しています。';
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="format-detection" content="telephone=no" />
  <meta property="og:site_name" content="ことれいのもり" />
  <meta property="og:url" content="<?= htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:image" content="<?= isset($article['thumbnail_path']) ? rtrim($config->get('BASE_URL'), '/') . '/' . ltrim(htmlspecialchars($article['thumbnail_path']), '/') : '' ?>" />
  <meta property="og:locale" content="ja_JP" />

  <script>
    const ADMIN_API_PATH = "<?= $config->get('urls')['admin_api']; ?>";
  </script>

  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/reset.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/prism.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/style.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/common.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/error.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/login.css" ?>>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/article.css" ?>>

  <!-- 管理画面の記事一覧ページに適用 -->
  <?php if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles'], '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href=<?= $config->get('paths')['css_admin'] . "/admin-articles.css" ?>>
  <?php endif; ?>

  <?php if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_create'], '/') . '$\/?/', $requestUri) || preg_match('/^' . preg_quote($config->get('urls')['admin_articles_edit'], '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href=<?= $config->get('paths')['css_admin'] . "/createArticle.css" ?>>
  <?php endif; ?>

  <link rel="icon" href="<?= $config->get('assets')['image'] . '/favicon.ico' ?>">
  <link rel="apple-touch-icon" href="<?= $config->get('assets')['image'] . '/webclip.png' ?>">

  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>


  <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') === false): ?>
    <!-- Google tag (gtag.js) -->
    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-QDDKCLS2FX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-QDDKCLS2FX');
  </script> -->
    <!-- Google AdSense -->
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4739065352463829"
      crossorigin="anonymous"></script> -->
  <?php endif; ?>
</head>