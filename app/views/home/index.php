<!DOCTYPE html>
<html lang="ja">

<?php include __DIR__ . '/../shared/head.php'; ?>

<body>
  <?php 
  require_once __DIR__ . '/../../functions.php';
  include __DIR__ . '/../shared/header.php';
  ?>

  <!-- コンテンツ -->
  <div class="wrapper" id="container">

    <!-- メインコンテンツ -->
    <main>
      <div class="main__content">
      <?php foreach ($articles as $article): ?>
        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking1.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date"><?= $article['updated_at'] ?></p>
            </div>
            <p class="content--card__title"><?= $article['title'] ?></p>
          </div>
        </article>
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
    <?php include __DIR__ . '/../home/shared/sidebar.php'; ?>
  </div>
<?php include __DIR__ .  '/../home/shared/footer.php' ?>
</body>
</html>