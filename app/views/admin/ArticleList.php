<!DOCTYPE html>
<html lang="ja">
<?php include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/shared/head.php'; ?>

<body>
  <?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/functions.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/shared/header.php';

  // エラーメッセージがあれば表示
  if (isset($_SESSION['error_message'])) {
    echo '<p class="error_message">' . $_SESSION['error_message'] . '</p>';
    unset($_SESSION['error_message']);
  }
  ?>

  <main>
    <h2 class="admin-headnig">記事一覧</h2>
    <a href="/pj_homepage/admin/articles/create">
      <button type="submit">新規作成</button>
    </a>
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
            <th>編集</th>
            <th>削除</th>
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
                <form action="/pj_homepage/admin/articles/edit" method="GET">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <button type="submit">編集</button>
                </form>
              </td>
              <td>
                <form action="/pj_homepage/admin/articles/delete" method="POST">
                  <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <button type="submit">削除</button>
                </form>
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