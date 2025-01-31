<?php
require_once __DIR__ . '/../../config.php';
require_once CONTROLLERS_PATH . '/BaseArticleController.php';


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
      'isEditMode' => $this->isEditMode,
      'formTitle' => $this->formTitle,
      'formAction' => $this->formAction,
      'articleTitle' => $this->articleTitle,
      'articleThumbnailPath' => $this->articleThumbnailPath,
      'articleContent' => $this->articleContent,
      'submitLabel' => $this->submitLabel,
      'articleId' => $this->articleId
    ];
    extract($viewData);

    include VIEWS_ADMIN_PATH . '/createArticle.php';
  }

  private function insertArticle()
  {
    $data = $this->getInputData();
    $thumbnailData = null;
    $articleImages = [];
    $imageData = [];

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

    if (!empty($_FILES['images']['name'][0])) {
      foreach ($_FILES['images']['name'] as $index => $imageName) {
        $file = [
          'name' => $_FILES['images']['name'][$index],
          'tmp_name' => $_FILES['images']['tmp_name'][$index],
          'type' => $_FILES['images']['type'][$index],
          'size' => $_FILES['images']['size'][$index],
          'error' => $_FILES['images']['error'][$index],
        ];

        $imageInfo = $this->uploadImage($file, null, $index);
        $imageData[] = [
          'tmp_path' => $imageInfo['tmp_path'],
          'file_name' => basename($imageInfo['url_path']),
          'file_path' => $imageInfo['url_path'],
          'alt_text' => $data['title'],
        ];
      }
    }

    // 記事挿入処理
    if ($this->validateArticleSave($data)) {
      $articleId = $this->articleModel->insertArticles($data['title'], $data['content'], $thumbnailData, $imageData);
      var_dump("記事を挿入しました");
      var_dump($imageData);
      echo $articleId;
      if (!$articleId) {
        echo "記事の挿入に失敗しました";
        return;
      }

      // サムネイル画像のリネーム
      if ($thumbnailData && $articleId) {
        $newFilePath = IMAGE_UPLOADS_THUMBNAILS_PATH . '/thumbnail_' . $articleId . '.' . pathinfo($thumbnailData['file_name'], PATHINFO_EXTENSION);
        if (rename($thumbnailData['tmp_path'], $newFilePath)) {
          $newFileName = basename($newFilePath);
          $relativePath = '/assets/image/uploads/thumbnails/' . $newFileName;
          $this->articleModel->updateThumbnailPath($articleId, $newFileName, $relativePath);
        } else {
          echo "サムネイル画像のリネームに失敗しました";
          return;
        }
      }
      // 記事画像のリネーム
      foreach ($imageData as $index => $image) {
        $newImageFileName = 'image_' . $articleId . ($index != null ? '_' . $index : '') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $newImageFilePath = IMAGE_UPLOADS_ARTICLES_PATH . $newImageFileName;

        if (rename($image['tmp_path'], $newImageFilePath)) {
          $image['file_name'] = $newImageFileName;
          $image['file_url'] = '/assets/image/uploads/articles/' . $newImageFileName;

          $this->articleModel->updateArticleImagePath($image['id'], $image['file_url']);
        } else {
          echo "画像のリネームに失敗しました";
          return;
        }
      }




      // header("Location:" . ADMIN_ARTICLES_URL);
      // exit;
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
    $this->isEditMode = false;
    $this->formTitle = '新規作成';
    $this->formAction = ADMIN_ARTICLES_CREATE_URL;
    $this->articleTitle = '';
    $this->articleThumbnailPath = '';
    $this->articleContent = '';
    $this->submitLabel = '送信';
    $this->articleId = '';
  }
}
