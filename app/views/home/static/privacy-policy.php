<!DOCTYPE html>
<html lang="ja">

<?php
$config = Config::getInstance();
$articleModel = new ArticleModel(Database::getInstance());
$categories = $articleModel->getCategoriesList();
include $config->get('paths')['global_shared'] . '/head.php';
?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>
  <div class="wrapper" id="container">
    <!-- コンテンツ -->
    <main>
      <article class="article">
        <div class="article-container">
          <!-- パンくずリスト -->
          <nav class="article__breadcrumb">
            <a href="/">ホーム</a> &gt;
            <a href="/privacy-policy">プライバシーポリシー</a>
          </nav>
          <!-- メインコンテンツ -->
          <div class="article__header">
            <h1 class="ariticle__title">プライバシーポリシー</h1>
            <div class="artical__date-wrapper">
              <i class="artical__icon fa-regular fa-clock"></i>
              <p class="article__date">2025-04-18</p>
            </div>
          </div>
          <h2 class="underline-with-background">プロフィール</h2>
          <p>はじめまして、管理人のことれいです。</p>
          <p>作成したゲームと技術記事を公開する「ことれいの森」を運営しています。</p>

          <h3>公開しているゲームについて</h3>
          <p>現在公開しているゲームは全て無料で遊ぶことができます。</p>
          <p>以前はGoogle Playストアにゲームを公開していましたが、APIレベル要件に対応できずに泣く泣く公開を中止した経緯があります。</p>
          <p>誰でもいつでも遊べるゲームを永久に残していきたい、という思いでこの「ことれいの森」の運営を始めました。</p>
          <p>ぜひ楽しんでください。</p>

          <h2 class="underline-with-background">掲載されている広告について</h2>
          <p>当サイトでは、今後Google AdSenseなどの第三者配信の広告サービスを利用する予定です。</p>
          <p>このような広告配信事業者は、ユーザーの興味に応じた広告を表示するためにCookie（クッキー）を使用することがあります。</p>
          <p>Googleの広告におけるCookieの取り扱いに関しては、<a href="https://policies.google.com/technologies/ads?hl=ja" target="_blank">Googleの広告に関するポリシー</a>をご覧ください。</p>

          <h2 class="underline-with-background">Cookieの使用について</h2>
          <p>当サイトでは、ユーザーの利便性向上やアクセス解析、広告配信のためにCookie（クッキー）を使用しています。</p>
          <p>Cookieとは、サイトにアクセスした際にブラウザに保存される情報であり、氏名や住所などの個人情報は含まれません。</p>
          <p>ブラウザの設定により、Cookieの使用を拒否することも可能です。ただし、拒否された場合は一部機能が正しく動作しない可能性があります。</p>

          <h2 class="underline-with-background">アクセス解析ツールについて</h2>
          <p>当サイトではGoogleが提供するアクセス解析ツール「Googleアナリティクス」を利用しています。</p>
          <p>このGoogleアナリティクスは、トラフィックデータの収集のためにCookieを使用しています。</p>
          <p>このデータは匿名で収集されており、個人を特定するものではありません。</p>
          <p>Googleアナリティクスに関する詳細は、<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank">Google アナリティクス利用規約</a>をご覧ください。</p>

          <h2 class="underline-with-background">著作権について</h2>
          <p>当サイトに掲載している文章・画像などの著作物について、無断転載・複製を固く禁じます。</p>
          <p>ただし、記事の一部を「引用の範囲内」で紹介していただくことは問題ありません。</p>
          <p>その際は、出典として「ことれいの森」のサイト名と、該当ページのURLを明記してください。</p>

          <h2 class="underline-with-background">免責事項</h2>
          <p>当サイトからリンクやバナーなどによって他のサイトに移動された場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。</p>
          <p>当サイトに掲載されている情報について、可能な限り正確な情報を掲載するよう努めていますが、誤情報が入り込んだり、情報が古くなっていることもあります。</p>
          <p>当サイトに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。</p>

          <h2 class="underline-with-background">プライバシーポリシーの変更について</h2>
          <p>本プライバシーポリシーの内容は、必要に応じて見直し・改善を行うことがあります。</p>

          <h2 class="underline-with-background">お問い合わせ</h2>
          <p>当サイトのプライバシーポリシーに関するお問い合わせは、以下のメールにてご連絡ください。</p>
          <p>メール: kotrebi9@gmail.com</p>
        </div>
      </article>
    </main>
    <?php include $config->get('paths')['views_home_shared'] . '/sidebar.php'; ?>
  </div>

  <?php include $config->get('paths')['views_home_shared'] .  '/footer.php' ?>
</body>

</html>