<?php if (!empty($relatedArticles)): ?>
  <section class="related-articles">
    <h2>同じカテゴリの最新記事</h2>
    <div class="related__content">
      <?php foreach ($relatedArticles as $article): ?>
        <article class="content-card">
          <a href="<?= '/' . htmlspecialchars($article['category_name']) . '/' . htmlspecialchars($article['slug']) . '-' . htmlspecialchars($article['id'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="content-card__body">
              <div class="related__content-card__container">
                <img src="<?= isset($article['thumbnail_path']) ? $config->get('BASE_URL') . htmlspecialchars($article['thumbnail_path'], ENT_QUOTES, 'UTF-8') : '' ?>" class="content-card__image">
              </div>
              <div class="content--card__date-wrapper">
                <i class="fa-regular fa-clock conten-card__icon"></i>
                <p class="content-card__date"><?= $article['formatted_date'] ?></p>
              </div>
              <p class="content--card__title"><?= $article['title'] ?></p>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>