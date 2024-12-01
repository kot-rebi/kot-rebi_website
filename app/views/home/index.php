<!DOCTYPE html>
<html lang="ja">

<?php include __DIR__ . '/../shared/head.php'; ?>

<body>
  <?php 
  require_once __DIR__ . '/../../functions.php';
  include __DIR__ . '/../shared/header.php';
  ?>

  <!-- コンテンツ -->
  <div class="wrapper" id="container">

    <!-- メインコンテンツ -->
    <main>
      <div class="main__content">

        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking1.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date">2024.11.14</p>
            </div>
            <p class="content--card__title">【PixiJS】deltaTimeの使い方について詳しく解説ああああああ</p>
          </div>
        </article>

        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking2.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date">2024.11.13</p>
            </div>
            <p class="content--card__title">ゲーム制作の技術記事を書いてみよう</p>
          </div>
        </article>

        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking3.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date">2024.11.12</p>
            </div>
            <p class="content--card__title">【テスト】テストテストテストテストテストテストテストテストテストテスト</p>
          </div>
        </article>

        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking4.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date">2024.11.11</p>
            </div>
            <p class="content--card__title">【テスト】テストテストテストテストテストテストテストテストテストテスト</>
          </div>
        </article>

        <article class="content-card">
          <div class="content-card__body">
            <img src="/pj_homepage/assets/image/img_ranking5.jpg" class="content-card__image">
            <div class="content--card__date-wrapper">
              <i class="fa-regular fa-clock conten-card__icon"></i>
              <p class="content-card__date">2024.11.10</p>
            </div>
            <p class="content--card__title">【テスト】テストテストテストテストテストテストテストテストテストテスト</p>
          </div>
        </article>

      </div>
    </main>

    <?php include __DIR__ . '/../home/shared/sidebar.php'; ?>
  </div>

<?php include __DIR__ .  '/../home/shared/footer.php' ?>

</body>
</html>