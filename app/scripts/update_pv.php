<?php

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/GoogleAnalyticsModel.php';
require_once __DIR__ . '/../controllers/admin/DailyArticleViewSaver.php';

$saver = new DailyArticleViewSaver();
$saver->updatePastViews('2025-02-14', '2025-04-28', 100);

// ログの保存
$logContent = ob_get_clean();
file_put_contents(__DIR__ . '/../logs/update_pv.log', $logContent . "\n", FILE_APPEND);

// エラーログの設定
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');