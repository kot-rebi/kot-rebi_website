<!DOCTYPE html>
<html lang="ja">
<?php
require_once __DIR__ . '/../../../config.php';
include GLOBAL_SHARED_PATH . '/head.php';
?>

<body>
  <?php
  require_once FUNCTIONS_PATH;
  $headerTitleText = "管理画面";
  include GLOBAL_SHARED_PATH . '/header.php';
  ?>
  <main>
    <div class="admin-create">
      <?php include VIEWS_ADMIN_PATH . '/articleForm.php'; ?>
    </div>
  </main>

  <footer>
    <script src=<?= JS_ADMIN_URL . "/textarea-auto-resize.js" ?>></script>
    <script src=<?= JS_ADMIN_URL . "/input-label-interaction.js" ?>></script>
    <script src=<?= JS_ADMIN_URL . "/imagePreview.js" ?>></script>
    <script src=<?= JS_ADMIN_URL . "/addArticleImages.js" ?>></script>
    <script src=<?= JS_ADMIN_URL . "/copy-to-clipboard.js" ?>> </script>
    <script src=<?= JS_ADMIN_URL . "/fileLabelUpdater.js" ?>></script>
  </footer>
</body>