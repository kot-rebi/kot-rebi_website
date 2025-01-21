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

    <!-- 現在のサムネイル -->
    <img id="thumbnailPreview" class="<?= $isEditMode && !empty($articleThumbnailPath) ? '' : 'admin-create__thumbnailHidden' ?>" src="<?= '/pj_homepage' . htmlspecialchars($articleThumbnailPath) ?? '' ?>" alt="現在のサムネイル画像">

    <!-- 新しいサムネイル -->
    <img id="newThumbnailPreview" class="admin-create__thumbnailHidden" src="" alt="新しいプレビュー">
  </div>

  <div class="admin-create__images">
    <label class="admin-create__label">画像と説明</label>
    <?php if ($isEditMode && !empty($articleImagesPath)): ?>
      <div class="inserted-image-box-container">
        <label>【挿入済み】</label>
        <div class="inserted-image-box">
          <?php for ($i = 0; $i < count($articleImagesPath); $i++): ?>
            <div class="inserted-image-set">

              <img src="<?= '/pj_homepage' .  htmlspecialchars($articleImagesPath[$i]['file_url']) ?>">
              <p>URL</p>
              <p><?= '/pj_homepage' . htmlspecialchars($articleImagesPath[$i]['file_url']) ?></p>
              <p>代替テキスト</p>
              <p><?= htmlspecialchars($articleImagesPath[$i]['alt_text']) ?></p>
              <label class="delete-button-label">
                <input type="checkbox" class="delete-button" name="delete_images[]" value="<?= htmlspecialchars($articleImagesPath[$i]['file_url']) ?>">
              </label>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="not-inserted-image-set">
      <label>【未挿入】</label>
      <div id="image-input-container">
        <div class="image-input-set">
          <input type="file" id="image" name="images[]" accept="image/*">
          <input type="text" id="alt_text" name="alt_texts[]" placeholder="画像の説明を入力">
        </div>
      </div>
      <button type="button" id="add-image-button">+ 画像を追加</button>
    </div>
  </div>

  <div class="admin-create__content">
    <label for="article-content">記事本文</label>
    <textarea name="content" id="article-content" rows="20" cols="50" placeholder="記事の本文を入力してください"><?= htmlspecialchars($articleContent) ?></textarea>
  </div>

  <input type="submit" value="<?= htmlspecialchars($submitLabel) ?>">
</form>