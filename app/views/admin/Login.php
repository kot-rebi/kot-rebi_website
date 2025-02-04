<!DOCTYPE html>
<html lang="ja">
<?php
$config = Config::getInstance();
include $config->get('paths')['global_shared'] . '/head.php';
?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>

  <main>
    <h2>ログイン</h2>
    <form method="POST">
      <label>ユーザー名: <input type="text" name="username"></label>
      <label>パスワード: <input type="password" name="password"></label>
      <button type="sumbit">ログイン</button>
    </form>
  </main>
  
</body>

</html>