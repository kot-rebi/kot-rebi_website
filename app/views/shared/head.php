<?php
$requestUri = $_SERVER['REQUEST_URI'];
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script>
    const CONTROLLERS_PATH = "<?= $config->get('paths')['controllers'] ?>";
  </script>

  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/reset.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/style.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/common.css" ?>>
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/error.css" ?>>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.css" rel="stylesheet">
  <link rel="stylesheet" href=<?= $config->get('paths')['css'] . "/article.css" ?>>

  <!-- 管理画面の記事一覧ページに適用 -->
  <?php if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles'], '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href=<?= $config->get('paths')['css_admin'] . "/admin-articles.css" ?>>
  <?php endif; ?>

  <?php if (preg_match('/^' . preg_quote($config->get('urls')['admin_articles_create'], '/') . '$\/?/', $requestUri) || preg_match('/^' . preg_quote($config->get('urls')['admin_articles_edit'], '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href=<?= $config->get('paths')['css_admin'] . "/createArticle.css" ?>>
  <?php endif; ?>

  <title>ことれいのもり</title>
</head>