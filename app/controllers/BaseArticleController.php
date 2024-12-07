<?php
require_once __DIR__ . '/../../config.php';
require_once MODELS_PATH . 'Database.php';
require MODELS_PATH . 'ArticleModel.php';

abstract class BaseArticleController
{
  protected $articleModel;

  /** 編集画面のh2に表示する文字列 @var string */
  protected $formTitle;

  /** 編集画面の遷移先URL @var string */
  protected $formAction;

  /** 記事タイトル @var string */
  protected $articleTitle;

  /** 記事内容 @var string */
  protected $articleContent;

  /** 決定ボタンの文字列 @var string */
  protected $submitLabel;

  /** 記事ID @var int */
  protected $articleId;

  public function __construct()
  {
    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  protected function getInputData()
  {
    return [
      'title' => $_POST['title'],
      'content' => $_POST['content']
    ];
  }

  /**
   * 記事IDを取得
   *
   * @return void
   */
  protected function getArticleID()
  {
    return $_GET['id'] ?? null;
  }

  /**
   * 記事IDのバリデーションチェック
   *
   * @param int $id 記事ID
   * @return void
   */
  protected function validateArticleId($id)
  {
    if (!isset($id) || !is_numeric($id)) {
      return false;
    }
    return true;
  }

  protected function validateArticleSave($data)
  {
    if (empty($data['title']) && empty($data['content'])) {
      echo "タイトルと記事内容は入力必須です";
      return false;
    }

    if (strlen($data['title']) > 255) {
      echo "タイトルは255文字以内で入力してください";
      return false;
    }

    return true;
  }
}
