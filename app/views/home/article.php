<!DOCTYPE html>
<html lang="ja">

<?php
$config = Config::getInstance();
include $config->get('paths')['global_shared'] . '/head.php';
?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>
  <div class="wrapper" id="container">
    <!-- コンテンツ -->
    <main>
      <article class="article">
        <div class="article-container">
          <!-- パンくずリスト -->
          <nav class="article__breadcrumb">
            <a href="/">ホーム</a> &gt;
            <a href="/<?= htmlspecialchars($category['slug']) ?>"><?= htmlspecialchars($category['name']) ?></a> &gt;
            <span><?= htmlspecialchars($article['title']) ?></span>
          </nav>
          <!-- メインコンテンツ -->
          <div class="article__header">
            <h1 class="ariticle__title"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="artical__date-wrapper">
              <i class="artical__icon fa-regular fa-clock"></i>
              <p class="article__date"><?= $article['formatted_date'] ?></p>
            </div>
            <div class="article__thumbnail">
              <img src="<?= isset($article['thumbnail_path']) ? $config->get('BASE_URL') . htmlspecialchars($article['thumbnail_path'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>
          </div>
          <?= $article['content_html'] ?>
        </div>

      </article>

    </main>
    <?php include $config->get('paths')['views_home_shared'] . '/sidebar.php'; ?>
  </div>

  <?php include $config->get('paths')['views_home_shared'] .  '/footer.php' ?>

</body>

</html>