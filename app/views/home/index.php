<!DOCTYPE html>
<html lang="ja">

<?php
$config = Config::getInstance();;
include $config->get('paths')['global_shared'] . '/head.php';
?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>

  <!-- コンテンツ -->
  <div class="wrapper" id="container">

    <!-- メインコンテンツ -->
    <main>
      <div class="main__content">
        <?php foreach ($articles as $article): ?>
          <article class="content-card">
            <a href=<?= $config->get('urls')['articles'] . '/' . htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8') ?>>
              <div class="content-card__body">
                <div class="content-card__container">
                  <img src="<?= isset($article['thumbnail_path']) ? $config->get('BASE_URL') . htmlspecialchars($article['thumbnail_path'], ENT_QUOTES, 'UTF-8') : '' ?>" class="content-card__image">
                </div>
                <div class="content--card__date-wrapper">
                  <i class="fa-regular fa-clock conten-card__icon"></i>
                  <p class="content-card__date"><?= $article['formatted_date'] ?></p>
                </div>
                <p class="content--card__title"><?= $article['title'] ?></p>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- ページネーション -->
      <div class="pagination">
        <a href="<?= $currentPage == 1 ? '#' : '?category=' . ($_GET['category'] ?? '') . '&page=' . ($currentPage - 1) ?>" class="<?= $currentPage == 1 ? 'disabled' : '' ?>">前へ</a>
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
          <a href="?category=<?= $_GET['category'] ?? '' ?>&page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a href="<?= $currentPage == $totalPages ? '#' : '?category=' . ($_GET['category'] ?? '') . '&page=' . ($currentPage + 1) ?>" class="<?= $currentPage == $totalPages ? 'disabled' : '' ?>">次へ</a>
      </div>
    </main>
    <?php include $config->get('paths')['views_home_shared'] . '/sidebar.php'; ?>
  </div>
  <?php include $config->get('paths')['views_home_shared'] .  '/footer.php' ?>
</body>

</html>