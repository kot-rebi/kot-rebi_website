<?php

require_once 'Database.php';

$db = new Database();

$result = $db->insertArticles("test12", "This is content12.");

if ($result) {
  echo "記事が挿入されました";
} else {
  echo "記事の挿入に失敗しました";
}
$articles = $db->getArticles();

echo "<pre>";
print_r($articles);
echo "<pre>";
