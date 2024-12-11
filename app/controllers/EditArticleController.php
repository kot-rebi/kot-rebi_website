<?php
require_once __DIR__ . '/../../config.php';
require_once CONTROLLERS_PATH . 'BaseArticleController.php';

class EditArticleController extends BaseArticleController
{


  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->displayEditForm();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->updateArticle();
    }
  }

  /**
   * 編集画面の表示
   *
   * @return void
   */
  private function displayEditForm()
  {
    $id = $this->getArticleID();
    if ($this->validateArticleId($id)) {
      $article = $this->articleModel->getAricleWithImagesById($id);
      $this->setArticleVariables($article);

      // ビューに渡す配列データ
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

      include VIEWS_ADMIN_PATH . 'createArticle.php';
    } else {
      header("Location: /error");
      exit;
    }
  }

  /**
   * 編集した記事を更新する
   *
   * @return void
   */
  private function updateArticle()
  {
    $id = $this->getArticleID();
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

    if ($this->validateArticleId($id)) {
      if ($this->validateArticleSave($data)) {
        // 記事の更新
        $this->articleModel->updateArticles($id, $data['title'], $data['content'], $thumbnailData);

        echo "記事を更新しました";

        // サムネイル画像のリネーム
        if ($thumbnailData && $id) {
          $newFilePath = IMAGE_UPLOADS_THUMBNAILS_PATH . 'thumbnail_' . $id . '.' . pathinfo($thumbnailData['file_name'], PATHINFO_EXTENSION);
          if (rename($thumbnailData['tmp_path'], $newFilePath)) {
            $newFileName = basename($newFilePath);
            $relativePath = '/assets/image/uploads/thumbnails/' . $newFileName;
            $this->articleModel->updateThumbnailPath($id, $newFileName, $relativePath);
          } else {
            echo "サムネイル画像のリネームに失敗しました";
            return;
          }
        }
        header("Location:" . ADMIN_ARTICLES_URL);
        exit;
      } else {
        echo "エラー: 記事の保存に失敗しました";
      }
    } else {
      echo "エラー: 記事IDの取得に失敗しました";
    }
  }

  /**
   * 編集用のviewの変数をセットする
   *
   * @param array $article DBに保存されている記事情報
   *  - string $article['id'] 記事のID
   *  - string $article['thumbnailPath'] | false 記事のサムネイルパス、ない場合はfalse
   *  - string $article['title'] 記事のタイトル
   *  - string $article['content'] 記事の本文
   * @return void
   */
  private function setArticleVariables($article)
  {
    $this->isEditMode = true;
    $this->formTitle = '編集';
    $this->formAction = ADMIN_ARTICLES_URL . 'edit?id=' . $article['id'];
    $this->articleTitle = $article['title'];
    if ($article['thumbnailPath'] === false) {
      $this->articleThumbnailPath = '';
    } else {
      $this->articleThumbnailPath = $article['thumbnailPath'];
    }
    $this->articleContent = $article['content'];
    $this->submitLabel = '更新';
    $this->articleId = $article['id'];
  }
}
