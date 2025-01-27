<!-- サイドバー -->
<aside class="main__sidebar">
  <div class="sidebar__content">

    <!-- 自己紹介ブロック -->
    <section class="profile">
      <img src="/pj_homepage/assets/image/img_Icon.png">
      <p class="profile__name">ことれい</p>
      <div class="profile__description">
        <p>ゲーム制作をしています</p>
        <p>誰かが遊んでくれたらうれしいです</p>
      </div>
    </section>


    <!-- 人気記事ブロック -->
    <section class="popular-article">
      <h3 class="sidebar__title">人気記事</h3>
      <li class="popular-article__item">
        <ul class="popular-article__list">
          <p class="prepare">準備中</p>
          <!-- <a href="#">
            <img src="/pj_homepage/assets/image/img_ranking1.jpg">
            <p>タイトル1</p>
          </a> -->
      </li>
      <!-- <li class="popular-article__item">
        <a href="#">
            <img src="/pj_homepage/assets/image/img_ranking2.jpg">
            <p>タイトル2</p>
          </a>
      </li>
      <li class="popular-article__item">
        <a href="#">
            <img src="/pj_homepage/assets/image/img_ranking3.jpg">
            <p>タイトル3</p>
          </a>
      </li> -->
      </ul>
    </section>

    <!-- カテゴリブロック -->
    <section class="categories">
      <h3 class="sidebar__title">カテゴリー</h3>
      <ul class="categories__list">
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $category) : ?>
            <li class="categories__item">
              <a href="?category=<?= $category['id'] ?>">
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