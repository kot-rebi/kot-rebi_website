<!DOCTYPE html>
<html lang="ja">

<?php
require_once __DIR__ . '/../../../config.php';
include GLOBAL_SHARED_PATH . '/head.php';
?>

<body>
  <?php
  require_once FUNCTIONS_PATH;
  include GLOBAL_SHARED_PATH . '/header.php';
  ?>
  <div class="wrapper" id="container">
    <!-- コンテンツ -->
    <main>
      <article class="article">
        <div class="article-container">
          <!-- メインコンテンツ -->
          <div class="article__header">
            <h1 class="ariticle__title"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="artical__date-wrapper">
              <i class="artical__icon fa-regular fa-clock"></i>
              <p class="article__date"><?= $article['formatted_date'] ?></p>
            </div>
            <div class="article__thumbnail">
            <img src="<?= isset($article['thumbnail_path']) ? BASE_URL . htmlspecialchars($article['thumbnail_path'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>
          </div>
          <?= $article['content_html'] ?>
        </div>

      </article>

    </main>
    <?php include VIEWS_HOME_SHARED_PATH . '/sidebar.php'; ?>
  </div>

  <?php include VIEWS_HOME_SHARED_PATH .  '/footer.php' ?>

</body>

</html>