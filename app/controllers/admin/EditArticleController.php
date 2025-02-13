<?php

class EditArticleController extends BaseArticleController
{
  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->displayEditForm();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once $this->config->get('paths')['models'] . '/CSRFProtection.php';
      $csrf = new CSRFProtection();
      if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $csrf->getToken()) {
        die("CSRF検証に失敗しました");
      }
      $this->updateArticle();
      $csrf->destroyToken();
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
        'articleImagesPath' => $this->articleImagesPath,
        'submitLabel' => $this->submitLabel,
        'metaTag' => $this->metaTag,
        'articleId' => $this->articleId
      ];
      extract($viewData);

      include $this->config->get('paths')['views_admin'] . '/createArticle.php';
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

    var_dump($data);
    if (isset($_POST['delete_images'])) {
      $deleteImages = $_POST['delete_images'];
      $this->articleModel->deleteArticleImage($id, $deleteImages);
    }

    $thumbnailData = null;
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

    if (!empty($_FILES['images']['name'][0])) {
      foreach ($_FILES['images']['name'] as $index => $imageName) {
        $file = [
          'name' => $_FILES['images']['name'][$index],
          'tmp_name' => $_FILES['images']['tmp_name'][$index],
          'type' => $_FILES['images']['type'][$index],
          'size' => $_FILES['images']['size'][$index],
          'error' => $_FILES['images']['error'][$index],
        ];

        $altText = isset($_POST['alt_texts'][$index]) ? $_POST['alt_texts'][$index] : '';

        $imageInfo = $this->uploadImage($file, null, $index);
        $imageData[] = [
          'tmp_path' => $imageInfo['tmp_path'],
          'file_name' => basename($imageInfo['url_path']),
          'file_path' => $imageInfo['url_path'],
          'alt_text' => $altText,
        ];
      }
    }

    // 既存画像の枚数を取得
    // 更新前の枚数に続いて連番でリネームするため、最後に追加された画像の末尾番号を取得
    /** @var int $existingImageCount */
    $existingImageCount = $this->articleModel->getUploadedImageNextNumber($id);
    echo "existingImageCount = " . $existingImageCount;
    if ($existingImageCount === -1) {
      echo "画像の番号のリネームに失敗しました";
      return;
    }

    if ($this->validateArticleId($id)) {
      if ($this->validateArticleSave($data)) {
        // 記事の更新
        $this->articleModel->updateArticles($id, $data['title'], $data['content'], $thumbnailData, $imageData, $data['meta_tag']);

        echo "記事を更新しました";

        // サムネイル画像のリネーム
        if ($thumbnailData && $id) {
          $newFilePath = $this->config->get('assets')['thumbnails'] . 'thumbnail_' . $id . '.' . pathinfo($thumbnailData['file_name'], PATHINFO_EXTENSION);
          if (rename($thumbnailData['tmp_path'], $newFilePath)) {
            $newFileName = basename($newFilePath);
            $relativePath = '/assets/image/uploads/thumbnails/' . $newFileName;
            $this->articleModel->updateThumbnailPath($id, $newFileName, $relativePath);
          } else {
            echo "サムネイル画像のリネームに失敗しました";
            return;
          }
        }

        echo "\n" . PHP_EOL;
        var_dump($imageData);


        // 記事画像のリネーム
        foreach ($imageData as $index => $image) {
          echo "foreachの中: " . $existingImageCount;
          $imageNumber = $existingImageCount + $index;
          $newImageFileName = '/image_' . $id . ($existingImageCount != 0 ? '_' . $imageNumber : '') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
          $newImageFilePath = $this->config->get('assets')['articles'] . $newImageFileName;

          echo 'New FILE NAME: ' . PHP_EOL;
          echo $imageNumber;
          echo $newImageFileName;
          echo $newImageFilePath;


          if (rename($image['tmp_path'], $newImageFilePath)) {
            $image['file_name'] = $newImageFileName;
            $image['file_url'] = '/assets/image/uploads/articles' . $newImageFileName;

            $this->articleModel->updateArticleImagePath($image['id'], $image['file_url']);
          } else {
            echo "画像のリネームに失敗しました";
            return;
          }
        }

        // header("Location:" . $this->config->get('urls')['admin_articles']);
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
    $this->formAction = $this->config->get('urls')['admin_articles'] . '/edit?id=' . $article['id'];
    $this->articleTitle = $article['title'];
    if ($article['thumbnailPath'] === false) {
      $this->articleThumbnailPath = '';
    } else {
      $this->articleThumbnailPath = $article['thumbnailPath'];
    }
    $this->articleContent = $article['content'];

    // 記事画像のパスが存在するか存在する
    if (isset($article['articleImagesPath']) && $article['articleImagesPath'] !== false) {
      $this->articleImagesPath = $article['articleImagesPath'];
    } else {
      $this->articleImagesPath = '';
    }
    $this->submitLabel = '更新';
    $this->metaTag = $article['meta_tag'];
    $this->articleId = $article['id'];
  }
}
