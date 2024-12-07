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
  include GLOBAL_SHARED_PATH . 'header.php';
  ?>
  <main>
    <div class="admin-create">
      <?php include VIEWS_ADMIN_PATH . 'articleForm.php'; ?>
    </div>
  </main>

  <footer>
    <script src="/pj_homepage/assets/js/admin/textarea-auto-resize.js"></script>
    <script src="/pj_homepage/assets/js/admin/input-label-interaction.js"></script>
  </footer>
</body>

