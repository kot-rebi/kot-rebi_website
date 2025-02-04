<!DOCTYPE html>
<html lang="ja">
<?php
$config = Config::getInstance();
include $config->get('paths')['global_shared'] . '/head.php';
?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  $headerTitleText = "管理画面";
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>
  <main>
    <div class="admin-create">
      <?php include $config->get('paths')['views_admin'] . '/articleForm.php'; ?>
    </div>
  </main>

  <footer>
    <script src=<?= $config->get('paths')['js_admin'] . "/textarea-auto-resize.js" ?>></script>
    <script src=<?= $config->get('paths')['js_admin'] . "/input-label-interaction.js" ?>></script>
    <script src=<?= $config->get('paths')['js_admin'] . "/imagePreview.js" ?>></script>
    <script src=<?= $config->get('paths')['js_admin'] . "/addArticleImages.js" ?>></script>
    <script src=<?= $config->get('paths')['js_admin'] . "/copy-to-clipboard.js" ?>> </script>
    <script src=<?= $config->get('paths')['js_admin'] . "/fileLabelUpdater.js" ?>></script>
  </footer>
</body>