<!DOCTYPE html>
<html lang="ja">
<?php
require_once __DIR__ . '/../../../config.php';
include GLOBAL_SHARED_PATH . '/head.php';
?>

<body>
  <?php
  require_once FUNCTIONS_PATH;
  include GLOBAL_SHARED_PATH . 'header.php';

  // エラーメッセージがあれば表示
  if (isset($_SESSION['error_message'])) {
    echo '<p class="error_message">' . $_SESSION['error_message'] . '</p>';
    unset($_SESSION['error_message']);
  }
  ?>

  <main>
    <h2 class="admin-headnig">記事一覧</h2>
    <a href="<?= ADMIN_ARTICLES_CREATE_URL ?>" class="admin-create-new-btn">
      新規作成
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
            <th>公開ステータス</th>
            <th>編集</th>
            <th>削除</th>
            <th>公開日</th>
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
                <div class="toggle-container">
                  <span class="toggle-text"><?= htmlspecialchars($article['is_published'] == 1 ? '公開' : '非公開') ?></span>
                  <label class="toggle-switch">
                    <input type="checkbox"
                      data-checkbox-id="<?= $article['id'] ?>"
                      onchange="togglePublish(<?= htmlspecialchars($article['id']) ?>)" <?= $article['is_published'] == 1 ? "checked" : '' ?>>
                    <span class="slider"></span>
                  </label>
                </div>
              </td>
              <td>
                <form action="/pj_homepage/admin/articles/edit" method="GET">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <button type="submit" class="admin-edit-btn">編集</button>
                </form>
              </td>
              <td>
                <form action="/pj_homepage/admin/articles/delete" method="POST">
                  <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <button type="submit" class="admin-delete-btn">削除</button>
                </form>
              </td>
              <td>
                <?php if ($article['scheduled_publish_date']): ?>
                  <span class="publish-date-display">
                    <?= htmlspecialchars(date('Y-m-d H:i', strtotime($article['scheduled_publish_date']))) ?>
                  </span>
                <?php else: ?>
                  <span class="publish-date-display">未設定</span>
                <?php endif; ?>
                <button type="button" class="publish-date-edit-btn" onclick="toggleScheduledPublishEdit(<?= $article['id'] ?>)">編集</button>
                <div id="edit-form-<?= $article['id'] ?>" class="edit-form" style="display: none;">
                  <form method="POST" action="/pj_homepage/admin/articles/update_publish_date">
                    <input type="datetime-local" class="publish-date-input-datetime" id="publish-datetime" name="scheduled_publish_date" value="<?= $article['scheduled_publish_date'] ? htmlspecialchars($article['scheduled_publish_date']) : '' ?>">
                    <input type="hidden" name="article_id" value="<?= htmlentities($article['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="publish-date-save-btn">保存</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer>
    <script src="/pj_homepage/assets/js/admin/toggle_publish.js"></script>
    <script src="/pj_homepage/assets/js/admin/scheduled-publish-editor.js"></script>
  </footer>
</body>

</html>