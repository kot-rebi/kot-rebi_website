<?php

/**
 * 新規作成/編集の共通フォーム
 * 
 * @param string $formTitle フォームの表示タイトル
 * @param string $formAction フォームの送信先URL
 * @param string $articleTitle 記事のタイトル
 * @param string $articleThumbnailPath 記事のサムネイルパス
 * @param string $articleContent 記事の本文
 * @param string $submitLabel ボタンのラベル
 * @param int|null $articleId 記事ID（編集時のみ使用する）
 */
?>
<h2 class="admin-create__header">
  <?= htmlspecialchars($formTitle) ?>
</h2>

<form action="<?= htmlspecialchars($formAction) ?>" method="POST" enctype="multipart/form-data">
  <?php if (isset($articleId)): ?>
    <input type="hidden" name="article_id" value="<?= htmlspecialchars($articleId) ?>">
  <?php endif; ?>

  <div class="admin-create__title">
    <label for="title">タイトル</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($articleTitle) ?>" placeholder="タイトルを入力してください">
  </div>

  <div class="admin-create__thumbnail">
    <label for="thumbnail">サムネイル画像</label>
    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
    <img id="thumbnailPreview" src="<?= '/pj_homepage' . htmlspecialchars($articleThumbnailPath) ?>" alt="プレビュー画像">
  </div>

  <div class="admin-create__content">
    <label for="article-content">記事本文</label>
    <textarea name="content" id="article-content" rows="20" cols="50" placeholder="記事の本文を入力してください"><?= htmlspecialchars($articleContent) ?></textarea>
  </div>
  
  <input type="submit" value="<?= htmlspecialchars($submitLabel) ?>">
</form>