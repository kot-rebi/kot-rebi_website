<!-- サイドバー -->
<aside class="main__sidebar">
  <div class="sidebar__content">

    <!-- 自己紹介ブロック -->
    <section class="profile">
      <img src=<?= $config->get('assets')['image'] . "/img_Icon.png" ?>>
      <p class="profile__name">ことれい</p>
      <div class="profile__description">
        <p>ゲーム制作をしています</p>
        <p>学んだことを記事にしていく予定です</p>
      </div>
    </section>


    <!-- 人気記事ブロック -->
    <section class="popular-article">
      <h3 class="sidebar__title">人気記事</h3>

      <ol class="popular-article__item">
        <?php if (!empty($popularArticles)): ?>
          <?php foreach ($popularArticles as $index => $article): ?>
            <li class="popular-article__list">
              <a href="<?= '/' . htmlspecialchars($article['category_name']) . '/' . htmlspecialchars($article['slug']) . '-' . htmlspecialchars($article['article_id'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="thumbnail-container">
                  <img src="<?= htmlspecialchars($article['thumbnail_path']) ?>">
                  <span class="ranking-badge <?= $index === 0 ? 'gold' : ($index === 1 ? 'silver' : 'bronze') ?>"><?= $index + 1 ?></span>
                </div>
                <p class="popular-article__title"><?= htmlspecialchars($article['title']) ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- 人気記事が一件もないとき or エラーのとき -->
          <li class="popular-article__item">
            <p class="prepare">準備中</p>
          </li>
        <?php endif; ?>
      </ol>
    </section>

    <!-- カテゴリブロック -->
    <section class="categories">
      <h3 class="sidebar__title">カテゴリー</h3>
      <ul class="categories__list">
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $category) : ?>
            <li class="categories__item">
              <a href=<?= $config->get('BASE_URL') . "/" . htmlspecialchars($category['slug']) ?>>
                <p><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="categories__item">
            <p>カテゴリーが存在しません</p>
          </li>
        <?php endif; ?>
      </ul>
    </section>
  </div>
</aside>