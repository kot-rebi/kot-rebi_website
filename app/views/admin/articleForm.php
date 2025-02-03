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
    <label for="thumbnail" class="file-input-label">サムネイル画像を選択</label>
    <input type="file" id="thumbnail" name="thumbnail" class="hidden-file-input" accept="image/*">

    <!-- 現在のサムネイル -->
    <img id="thumbnailPreview" class="<?= $isEditMode && !empty($articleThumbnailPath) ? '' : 'admin-create__thumbnailHidden' ?>" src="<?= $config->get('BASE_URL') . htmlspecialchars($articleThumbnailPath) ?? '' ?>" alt="現在のサムネイル画像">

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

              <img src="<?= $config->get('BASE_URL') .  htmlspecialchars($articleImagesPath[$i]['file_url']) ?>">
              <p>URL</p>
              <div class="url-container">
                <span class="url-text" id="url-<?= $i ?>" title="<?= $config->get('BASE_URL') . htmlspecialchars($articleImagesPath[$i]['file_url']) ?>">
                  <?= $config->get('BASE_URL') . htmlspecialchars($articleImagesPath[$i]['file_url']) ?>
                </span>
                <button type="button" class="copy-button" data-target="url-<?= $i ?>"><i class="fa-regular fa-copy"></i></button>
              </div>

              <p>altテキスト</p>
              <div class="alt-container">
                <p id="alt-<= $i ?>" class="alt-text"><?= htmlspecialchars($articleImagesPath[$i]['alt_text']) ?></p>
                <button type="button" class="copy-button" data-target="alt-<? $i ?>"><i class="fa-regular fa-copy"></i></button>
              </div>

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
          <label for="image" class="file-input-label">画像を選択</label>
          <input type="file" id="image" class="hidden-file-input" name="images[]" accept="image/*">
          <input type="text" id="alt_text" name="alt_texts[]" placeholder="altテキスト">
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