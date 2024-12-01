<!DOCTYPE html>
<html lang="ja">

<?php include __DIR__ . '/../shared/head.php'; ?>

<body>
  <?php
  require_once __DIR__ . '/../../functions.php';
  include __DIR__ . '/../shared/header.php'; 
  ?>
  <div class="wrapper" id="container">
    <!-- コンテンツ -->
    <main>
      <article class="article">
        <div class="article-container">
          <!-- メインコンテンツ -->
          <div class="article__header">
            <h1 class="ariticle__title">PixiJSdeltaTimeの使い方について詳しく解説ああああああ</h1>
            <div class="artical__date-wrapper">
              <i class="artical__icon fa-regular fa-clock"></i>
              <p class="article__date">2024.11.14</p>
            </div>
            <div class="article__thumbnail">
              <img src="/pj_homepage/assets/image/img_ranking1.jpg">
            </div>
          </div>

          <div class="article__content">
            <h2 class="underline-with-background">PixJSとは</h2>
            <p>PixiJSとは、Javascriptで使うことができます。というような説明の文章を入れる部分です。</p>
            <p>参考リンクがある場合は、<a href="#">ここを</a>クリックしてください。</p>
          </div>

          <div class="article__content">
            <h3 class="orange-underline-heading">PixiJSの歴史</h3>
            <ul>
              <li>2002年</li>
              <li>2010年</li>
              <li>2022年</li>
            </ul>
            <p>こんな感じで、リスト形式にも並べることができます</p>
            <p>コードブロックの機能を追加しました。</p>
            <div class="code-block">
              <pre class="line-numbers" data-copy="true"><code class="language-javascript">
function greet() {
  console.log('Hello, world!');
}
              </code></pre>
            </div>
            <p>コードを表示することができますし、コピーもできます</p>
          </div>

          <div class="article__content">
            <h2 class="underline-with-background">まとめ</h2>
            <p>こんな感じで記事を増やしていこう</p>
            <p>やること残り</p>
            <ul>
              <li>コードブロックの作成</li>
              <li>Shareボタン</li>
              <li>カテゴリの表示</li>
              <li>前の記事と次の記事</li>
              <li>レスポンス時の見た目調整</li>
            </ul>
          </div>
        </div>

      </article>

    </main>
    <?php include __DIR__ . '/../home/shared/sidebar.php'; ?>
  </div>

  <?php include __DIR__ .  '/../home/shared/footer.php' ?>

</body>

</html>