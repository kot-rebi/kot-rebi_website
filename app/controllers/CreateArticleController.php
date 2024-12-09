<?php
require_once __DIR__ . '/../../config.php';
require_once CONTROLLERS_PATH . 'BaseArticleController.php';


class CreateArticleController extends BaseArticleController
{

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->displayCreateForm();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->insertArticle();
    }
  }

  private function displayCreateForm()
  {
    $this->setArticleVariables();
    $viewData = [
      'formTitle' => $this->formTitle,
      'formAction' => $this->formAction,
      'articleTitle' => $this->articleTitle,
      'articleContent' => $this->articleContent,
      'submitLabel' => $this->submitLabel,
      'articleId' => $this->articleId
    ];
    extract($viewData);

    include VIEWS_ADMIN_PATH . 'createArticle.php';
  }

  private function insertArticle()
  {
    $data = $this->getInputData();
    $thumbnailData = null;

    // サムネイル画像の仮保存
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
      $thumbnailInfo = $this->uploadThumbnail($_FILES['thumbnail']);
      if ($thumbnailInfo) {
        $thumbnailData = [
          'tmp_path' => $thumbnailInfo['tmp_path'],
          'file_name' => basename($thumbnailInfo['url_path']),
          'file_path' => $thumbnailInfo['url_path'],
          "alt_text" => $data['title'],
        ];
      }
    }

    if ($thumbnailData === null) {
      echo "サムネイル画像が必要です";
      return;
    }

    // 記事挿入処理
    if ($this->validateArticleSave($data)) {
      $articleId = $this->articleModel->insertArticles($data['title'], $data['content'], $thumbnailData);
      echo $articleId;
      if (!$articleId) {
        echo "記事の挿入に失敗しました";
        return;
      }

      // サムネイル画像のリネーム
      if ($thumbnailData && $articleId) {
        $newFileName = IMAGE_UPLOADS_THUMBNAILS_PATH . 'thumbnail_' . $articleId . '.' . pathinfo($thumbnailData['file_name'], PATHINFO_EXTENSION);
        rename($thumbnailData['tmp_path'], $newFileName);
      }

      header("Location:" . ADMIN_ARTICLES_URL);
      exit;
    } else {
      echo "記事の保存に失敗しました";
    }
  }

  /**
   * 新規作成用のviewの変数をセットする
   * 
   * @return void
   */
  private function setArticleVariables()
  {
    $this->formTitle = '新規作成';
    $this->formAction = ADMIN_ARTICLES_CREATE_URL;
    $this->articleTitle = '';
    $this->articleContent = '';
    $this->submitLabel = '送信';
    $this->articleId = '';
  }
}
