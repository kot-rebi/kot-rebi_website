<?php
$config = Config::getInstance();
require_once $config->get('paths')['models'] . '/CSRFProtection.php';

$csrf = new CSRFProtection();
$csrf->generateToken();
?>

<!DOCTYPE html>
<html lang="ja">
<?php include $config->get('paths')['global_shared'] . '/head.php'; ?>

<body>
  <?php
  require_once $config->get('FUNCTIONS_PATH');
  include $config->get('paths')['global_shared'] . '/header.php';
  ?>

  <main>

    <form method="POST" class="form-wrapper">
      <div class="form-group">
        <h2>ログイン</h2>
      </div>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf->getToken(), ENT_QUOTES, 'UTF-8'); ?>">

      <div class="form-group">
        <label>ユーザー名: <input type="text" name="username"></label>
      </div>

      <div class="form-group">
        <label>パスワード: <input type="password" name="password"></label>
      </div>

      <div class="form-group">
        <button type="submit">ログイン</button>
      </div>
    </form>
  </main>

</body>

</html>