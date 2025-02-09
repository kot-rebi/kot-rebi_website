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

  <!-- コンテンツ -->
  <div class="wrapper" id="container">
    <main>
      <article class="article">
        <div class="article-container_error">
          <h1>404 Not Found</h1>
          <p>&nbsp;</p>
          <p>お探しのページは見つかりませんでした</p>
          <p>&nbsp;</p>
          <p><a href="<?= $config->get('BASE_URL') ?>">ホームに戻る</a></p>
        </div>
      </article>
    </main>


    <!-- <?php // include $config->get('paths')['views_home_shared'] . '/sidebar.php'; 
          ?> -->

  </div>
  <?php include $config->get('paths')['views_home_shared'] .  '/footer.php' ?>
</body>

</html>