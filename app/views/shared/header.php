<!-- ヘッダー -->
<header class="header">
  <?php $config = Config::getInstance(); ?>
  <!-- <img src="<?= $config->get('assets')['image']  . "/img_profile3.png" ?>" class="header__icon"> -->
  <img src="/assets/image/img_profile3.png" class="header__icon">
  <a href= "/" class="header_title"><?= getHeaderTitle() ?></a>
  <img src="<?= $config->get('assets')['image']  . "/img_profile2.png" ?>" class="header__icon">
  <!-- <nav class="header_navigation">
      <ul class="header_navigation_list">
        <li class="header_navigation_item"><a href="#">ホーム</a></li>
        <li class="header_navigation_item"><a href="#">ゲーム</a></li>
        <li class="header_navigation_item"><a href="#">技術記事</a></li>
        <li class="header_navigation_item"><a href="#">その他</a></li>
      </ul>
    </nav> -->
</header>