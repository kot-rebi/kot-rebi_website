<!DOCTYPE html>
<html lang="ja">

<?php include __DIR__ . '/../shared/head.php'; ?>

<body>
  <?php
  require_once __DIR__ . '/../../functions.php';
  include __DIR__ . '/../shared/header.php'; 
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
              <p class="article__date">2024.11.14</p>
            </div>
            <div class="article__thumbnail">
              <img src="/pj_homepage/assets/image/img_ranking1.jpg">
            </div>
          </div>
          <?= $article['content_html'] ?>
        </div>

      </article>

    </main>
    <?php include __DIR__ . '/../home/shared/sidebar.php'; ?>
  </div>

  <?php include __DIR__ .  '/../home/shared/footer.php' ?>

</body>

</html>