<?php

abstract class BaseArticleController
{
  protected $articleModel;
  protected $config;


  /** 編集画面かどうか @var bool */
  protected $isEditMode;

  /** 編集画面のh2に表示する文字列 @var string */
  protected $formTitle;

  /** 編集画面の遷移先URL @var string */
  protected $formAction;

  /** 記事タイトル @var string */
  protected $articleTitle;

  /** 記事のサムネイルパス @var string */
  protected $articleThumbnailPath;

  /** 記事内容 @var string */
  protected $articleContent;

  /** 記事挿入画像 @var string */
  protected $articleImagesPath;

  /** 決定ボタンの文字列 @var string */
  protected $submitLabel;

  /** メタタグの文字列 @var string */
  protected $metaTag;

  /** 記事ID @var int */
  protected $articleId;

  /** カテゴリー一覧 @var array */
  protected $categories;

  /** カテゴリーID @var int */
  protected $categoryId;

  public function __construct()
  {
    $this->config = Config::getInstance();

    $this->articleModel = new ArticleModel(Database::getInstance());
  }

  protected function getInputData()
  {
    return [
      'title' => $_POST['title'],
      'content' => $_POST['content'],
      'category_id' => $_POST['category'],
      'meta_tag' => $_POST['meta_tag']
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

  /**
   * サムネイル画像をアップロードする
   *
   * @param array $file $_FILES['thumbnail]のようなデータ
   * @param integer $articleId 記事ID
   * @return string|null アップロード後の画像パス、または null
   */
  protected function uploadThumbnail($file, $articleId = null)
  {
    $validationResult = $this->validateThumbnail($file);
    if (!$validationResult) {
      return $validationResult;
    }

    // 仮のファイル名生成（記事IDがない場合は一時的にユニークIDを使用）
    $uploadDirectory = $this->config->get('assets')['thumbnails'];
    $fileName = $articleId
      ? '/thumbnail_' . $articleId . '.' . pathinfo($file['name'], PATHINFO_EXTENSION)
      : '/temp_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadPath = $uploadDirectory . $fileName;

    echo "uploadPath: " . $uploadPath;
    echo "url_path: " . '/assets/image/uploads/thumbnails' . $fileName;

    // 画像圧縮
    $compressedPath = $this->compressImage($file['tmp_name'], $uploadPath, $file['type']);
    if ($compressedPath) {
      return [
        'tmp_path' => $uploadPath,
        'url_path' => '/assets/image/uploads/thumbnails' . $fileName
      ];
    }

    return null;
  }


  /**
   * 画像を圧縮する
   *
   * @param string $sourcePath 圧縮対象のパス
   * @param string $destinationPath 圧縮後に保存するファイルパス
   * @param string $mimeType 画像のMIMEタイプ
   * @param integer $quality 画像の圧縮品質
   * @return string|false 圧縮後の画像パス、または false
   */
  private function compressImage($sourcePath, $destinationPath, $mimeType, $quality = 75)
  {
    echo "画像の圧縮後のパス: " . $destinationPath;
    // PHPで扱いやすいように変換→圧縮→保存の過程を辿る
    switch ($mimeType) {
      case 'image/jpeg':
        $image = imagecreatefromjpeg($sourcePath);
        if ($image === false) {
          return false;
        }
        $result = imagejpeg($image, $destinationPath, $quality);
        break;

      case 'image/png':
        $image = imagecreatefrompng($sourcePath);
        if ($image === false) {
          return false;
        }
        $result = imagepng($image, $destinationPath, 9);
        break;

      case 'image/gif':
        $image = imagecreatefromgif($sourcePath);
        if ($image === false) {
          return false;
        }
        // gifは圧縮なし
        $result = imagegif($image, $destinationPath);
        break;

      default:
        // サポートしていない形式
        return false;
    }

    // メモリ解放
    if ($image) {
      imagedestroy($image);
    }

    if ($result) {
      return $destinationPath;
    } else {
      return false;
    }
  }

  /**
   * サムネイル画像を削除する
   *
   * @param string $imagePath 削除するサムネイル画像のパス
   * @return bool 削除が成功したかどうか
   */
  protected function deleteThumbnail($imagePath)
  {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

    if (file_exists($filePath)) {
      if (unlink($filePath)) {
        return true;
      } else {
        return false;
      }
    } else {
      // ファイルが存在しない
      return false;
    }
  }

  /**
   * サムネイル画像のバリデーション
   *
   * @param array $file $_FILE['thumbnail]みたいなデータ
   * @return bool|string バリデーション結果のエラーメッセージ または true
   */
  protected function validateThumbnail($file)
  {
    // ファイルのエラー確認
    if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
      echo 'ファイルのアップロードに失敗しました';
      return false;
    }

    // ファイルタイプと拡張子を調べ、両方一致するかチェック
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedImageType = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array(strtolower($fileExtension), $allowedExtensions) || !in_array($file['type'], $allowedImageType)) {
      echo '許可されていない画像タイプです';
      return false;
    }

    // ファイルのサイズチェック
    $maxFileSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxFileSize) {
      echo 'ファイルが大きすぎます 最大10MBです';
      return false;
    }

    return true;
  }


  protected function uploadImage($file, $articleId = null, $index)
  {
    $uploadDir = $this->config->get('assets')['articles'];

    $fileName = $articleId
      ? 'image_' . $articleId . ($index != null ? '_' . $index : '') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION)
      : 'temp_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

    $filePath = $uploadDir . $fileName;

    $compressedPath = $this->compressImage($file['tmp_name'], $filePath, $file['type']);

    if ($compressedPath) {
      return [
        'tmp_path' => $filePath,
        'url_path' => '/assets/image/uploads/articles/' . $fileName,
      ];
    }

    return null;
  }
}
