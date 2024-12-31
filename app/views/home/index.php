<!DOCTYPE html>
<html lang="ja">

<?php
require_once __DIR__ . '/../../../config.php';
include GLOBAL_SHARED_PATH . '/head.php';
?>

<body>
  <?php
  require_once FUNCTIONS_PATH;
  include GLOBAL_SHARED_PATH . 'header.php';
  ?>

  <!-- コンテンツ -->
  <div class="wrapper" id="container">

    <!-- メインコンテンツ -->
    <main>
      <div class="main__content">
        <?php foreach ($articles as $article): ?>
          <a href="/pj_homepage/articles/<?= htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8') ?>">
            <article class="content-card">
              <div class="content-card__body">
                <img src="<?= isset($article['thumbnail_path']) ? '/pj_homepage' . htmlspecialchars($article['thumbnail_path'], ENT_QUOTES, 'UTF-8') : '' ?>" class="content-card__image">
                <div class="content--card__date-wrapper">
                  <i class="fa-regular fa-clock conten-card__icon"></i>
                  <p class="content-card__date"><?= $article['formatted_date'] ?></p>
                </div>
                <p class="content--card__title"><?= $article['title'] ?></p>
              </div>
            </article>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="pagination">
        <a href="<?= $currentPage == 1 ? '#' : '?page=' . ($currentPage - 1) ?>" class="<?= $currentPage == 1 ? 'disabled' : '' ?>">前へ</a>
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
          <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a href="<?= $currentPage == $totalPages ? '#' : '?page=' . ($currentPage + 1) ?>" class="<?= $currentPage == $totalPages ? 'disabled' : '' ?>">次へ</a>
      </div>
    </main>
    <?php include VIEWS_HOME_SHARED_PATH . 'sidebar.php'; ?>
  </div>
  <?php include VIEWS_HOME_SHARED_PATH .  'footer.php' ?>
</body>

</html>