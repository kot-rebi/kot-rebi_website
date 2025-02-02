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

  <!-- コンテンツ -->
  <div class="wrapper" id="container">
    <main>
      <article class="article">
        <div class="article-container_error">
          <h1>404 Not Found</h1>
          <p>&nbsp;</p>
          <p>お探しのページは見つかりませんでした</p>
          <p>&nbsp;</p>
          <p><a href="<?= BASE_URL ?>">ホームに戻る</a></p>
        </div>
      </article>
    </main>


    <!-- <?php // include VIEWS_HOME_SHARED_PATH . '/sidebar.php'; ?> -->

  </div>
  <?php include VIEWS_HOME_SHARED_PATH .  '/footer.php' ?>
</body>

</html>