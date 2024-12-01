<!DOCTYPE html>
<html lang="ja">
<?php include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/shared/head.php'; ?>

<body>
  <?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/functions.php';
  $headerTitleText = "管理画面";
  include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/shared/header.php';
  ?>
  <main>
    <div class="admin-create">
      <?php include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/admin/articleForm.php'; ?>
    </div>
  </main>

  <footer>
    <script src="/pj_homepage/assets/js/admin/textarea-auto-resize.js"></script>
    <script src="/pj_homepage/assets/js/admin/input-label-interaction.js"></script>
  </footer>
</body>

