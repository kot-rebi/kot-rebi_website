

<!DOCTYPE html>
<html lang="ja">
<?php include '../includes/head.php'; ?>

<body>
  <?php
  require '/functions.php';
  $headerTitleText = "管理画面";
  include '../includes/header.php';
  ?>

  <main>
    <h2 class="admin-headnig">記事一覧</h2>
    <div class="pagination">
      <a href="<?= $currentPage == 1 ? '#' : '?page=' . ($currentPage - 1) ?>" class="<?= $currentPage == 1 ? 'disabled' : '' ?>">前へ</a>
      <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
      <a href="<?= $currentPage == $totalPages ? '#' : '?page=' . ($currentPage + 1) ?>" class="<?= $currentPage == $totalPages ? 'disabled' : '' ?>">次へ</a>
    </div>
    <div class="admin-table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>タイトル</th>
            <th>作成日</th>
            <th>更新日</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($articles as $article): ?>
            <tr>
              <td><?= htmlspecialchars($article['id']) ?></td>
              <td><?= htmlspecialchars($article['title']) ?></td>
              <td><?= htmlspecialchars($article['created_at']) ?></td>
              <td><?= htmlspecialchars($article['updated_at']) ?></td>
              <td>
                <a href="#">編集</a>
                <a href="#">削除</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <?php //include '../includes/footer.php'; 
  ?>
</body>

</html>