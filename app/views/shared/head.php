<?php 
$requestUri = $_SERVER['REQUEST_URI'];
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <script>
    const BASE_URL = "<?= BASE_URL ?>";
    const APP_URL = "<?= APP_URL ?>";
    const CONTROLLERS_URL = "<?= CONTROLLERS_URL ?>";
  </script>
  
  <link rel="stylesheet" href= <?= CSS_URL . "/reset.css" ?> >
  <link rel="stylesheet" href= <?= CSS_URL . "/style.css" ?> >
  <link rel="stylesheet" href= <?= CSS_URL . "/common.css" ?> >
  <link rel="stylesheet" href= <?= CSS_URL . "/error.css" ?> >
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.css" rel="stylesheet">
  <link rel="stylesheet" href= <?= CSS_URL . "/article.css" ?> >

  <!-- 管理画面の記事一覧ページに適用 -->
  <?php if (preg_match('/^' . preg_quote(ADMIN_ARTICLES_URL, '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href= <?= CSS_ADMIN_URL . "/admin-articles.css" ?> >
  <?php endif; ?>

  <?php if (preg_match('/^' . preg_quote(ADMIN_ARTICLES_CREATE_URL, '/') . '$\/?/', $requestUri) || preg_match('/^' . preg_quote(ADMIN_ARTICLES_EDIT_URL, '/') . '\/?/', $requestUri)): ?>
    <link rel="stylesheet" href= <?= CSS_ADMIN_URL ."/createArticle.css" ?> >
  <?php endif; ?>

  <title>ことれいのもり</title>
</head>