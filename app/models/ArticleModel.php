<?php

/**
 * 記事に関するデータ操作を管理するクラス
 */
class ArticleModel
{
  private $db;

  /**
   * コンストラクタ
   * Database クラスからデータベース接続を受け取る
   */
  public function __construct()
  {
    $this->db = Database::getInstance()->getConnection();
  }

  /**
   * 記事一覧を取得
   *
   * @param int $limit 1ページあたりの取得する記事の件数
   * @param int $offset ページの開始位置（0からはじまる）
   * @return array 記事の配列
   */
  public function getArticles($limit, $offset)
  {
    try {
      $stmt = $this->db->prepare("SELECT * FROM " . TABLE_ARTICLES . "  LIMIT :limit OFFSET :offset");
      $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "データの取得に失敗しました: " . $e->getMessage();
      return [];
    }
  }

  public function getPublishedArticles($limit, $offset)
  {
    try {
      $stmt = $this->db->prepare("
      SELECT 
        a.id, 
        a.title, 
        DATE(a.updated_at) AS formatted_date,
        i.file_path AS thumbnail_path
      FROM " . TABLE_ARTICLES . " a
      LEFT JOIN " . TABLE_THUMBNAILS . " i ON a.id = i.article_id
      WHERE a.is_published = 1
      ORDER BY a.updated_at DESC
      LIMIT :limit OFFSET :offset
      ");
      $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "データの取得に失敗しました: " . $e->getMessage();
      return [];
    }
  }

  /**
   * 記事の総件数を取得する
   *
   * @return int 記事の総件数
   */
  public function getTotalArticles()
  {
    try {
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM " . TABLE_ARTICLES);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['total'];
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました: " . $e->getMessage();
      return 0;
    }
  }

  public function getTotalPublishedArticles()
  {
    try {
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM " . TABLE_ARTICLES . " WHERE is_published = 1");
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['total'];
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました: " . $e->getMessage();
      return 0;
    }
  }

  /**
   * 新しい記事を挿入
   *
   * @param string $title 記事のタイトル
   * @param string $content 記事の内容
   * @param array|null $thumbnailData サムネイル画像ファイル
   * @param array|null $imageData 記事の画像ファイル
   * @return int|null 成功したときは記事ID、失敗したときはfalse
   */
  public function insertArticles($title, $content, $thumbnailData, &$imageData)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の挿入
      $stmt = $this->db->prepare("INSERT INTO " . TABLE_ARTICLES . " (title, content) VALUES (:title, :content)");
      $stmt->bindValue(":title", $title, PDO::PARAM_STR);
      $stmt->bindValue(":content", $content, PDO::PARAM_STR);
      $stmt->execute();

      $articleId = $this->db->lastInsertId();
      if (!$articleId) {
        throw new Exception("記事IDの取得に失敗しました");
      }

      // サムネイル画像はimagesテーブルに保存
      if ($thumbnailData) {
        $stmtImage = $this->db->prepare("
        INSERT INTO " . TABLE_THUMBNAILS . " (article_id, file_name, file_path, alt_text, upload_date)
        VALUES (:article_id, :file_name, :file_path, :alt_text, NOW())
        ");
        $stmtImage->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmtImage->bindValue(":file_name", $thumbnailData['file_name'], PDO::PARAM_STR);
        $stmtImage->bindValue(":file_path", $thumbnailData['file_path'], PDO::PARAM_STR);
        $stmtImage->bindValue(":alt_text", $thumbnailData['alt_text'], PDO::PARAM_STR);
        $stmtImage->execute();
      }

      // 記事の画像を挿入し、挿入後のIDを取得
      if ($imageData) {
        $stmtUploadedImages = $this->db->prepare("
        INSERT INTO " . TABLE_IMAGES . " (article_id, file_url, alt_text, uploaded_at)
        VALUES (:article_id, :file_url, :alt_text, NOW())
        ");

        foreach ($imageData as $index => &$image) {
          $stmtUploadedImages->bindValue(":article_id", $articleId, PDO::PARAM_INT);
          $stmtUploadedImages->bindValue(":file_url", $image['file_path'], PDO::PARAM_STR);
          $stmtUploadedImages->bindValue(":alt_text", $image['alt_text'], PDO::PARAM_STR);
          $stmtUploadedImages->execute();

          $imageData[$index]['id'] = $this->db->lastInsertId();
        }
      }

      $this->db->commit();
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);

      return $articleId;
    } catch (PDOException $e) {
      $this->db->rollBack();
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      echo "記事の挿入に失敗しました: " . $e->getMessage();
      return false;
    } catch (Exception $e) {
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      echo "エラー: " . $e->getMessage();
      return false;
    }
  }

  /**
   * 記事の内容を更新
   *
   * @param int $id 更新する記事のID
   * @param string $title 更新後のタイトル
   * @param string $content 更新後の内容
   * @return bool 成功したときはtrue、失敗したときはfalse
   */
  public function updateArticles($id, $title, $content, $thumbnailData = null, &$imageData)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の更新
      $stmt = $this->db->prepare("
        UPDATE " . TABLE_ARTICLES . "
        SET title = :title, content = :content, updated_at = NOW()
        WHERE id = :id
      ");
      $stmt->bindValue(":title", $title, PDO::PARAM_STR);
      $stmt->bindValue(":content", $content, PDO::PARAM_STR);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      if (!$stmt->execute()) {
        throw new Exception("記事の更新に失敗しました");
      }

      // サムネイル画像の更新
      if ($thumbnailData) {
        // 新しいサムネイルを挿入
        $stmtImage = $this->db->prepare("
          INSERT INTO " . TABLE_THUMBNAILS . " (article_id, file_name, file_path, alt_text, upload_date)
          VALUES (:article_id, :file_name, :file_path, :alt_text, NOW())
        ");
        $stmtImage->bindValue(":article_id", $id, PDO::PARAM_INT);
        $stmtImage->bindValue(":file_name", $thumbnailData['file_name'], PDO::PARAM_STR);
        $stmtImage->bindValue(":file_path", $thumbnailData['file_path'], PDO::PARAM_STR);
        $stmtImage->bindValue(":alt_text", $thumbnailData['alt_text'], PDO::PARAM_STR);
        if (!$stmtImage->execute()) {
          throw new Exception("サムネイルの挿入に失敗しました");
        }

        // 古い画像を削除
        if (!$this->deleteThumbnail($id)) {
          throw new Exception("古いサムネイル画像の削除に失敗しました");
        }
      }

      // 記事の画像を挿入し、挿入後のIDを取得
      if ($imageData) {
        $stmtUploadedImages = $this->db->prepare("
        INSERT INTO " . TABLE_IMAGES . " (article_id, file_url, alt_text, uploaded_at)
        VALUES (:article_id, :file_url, :alt_text, NOW())
        ");

        foreach ($imageData as $index => &$image) {
          $stmtUploadedImages->bindValue(":article_id", $id, PDO::PARAM_INT);
          $stmtUploadedImages->bindValue(":file_url", $image['file_path'], PDO::PARAM_STR);
          $stmtUploadedImages->bindValue(":alt_text", $image['alt_text'], PDO::PARAM_STR);
          $stmtUploadedImages->execute();

          $imageData[$index]['id'] = $this->db->lastInsertId();
        }
      }

      $this->db->commit();
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);

      echo "記事の更新が成功しました";
      return true;
    } catch (PDOException $e) {
      $this->db->rollBack();
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      echo "記事の更新に失敗しました: " . $e->getMessage();
      return false;
    } catch (Exception $e) {
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
      echo "エラー: " . $e->getMessage();
      return false;
    }
  }

  public function updateThumbnailPath($articleId, $newFileName, $newFilePath)
  {
    try {
      $stmt = $this->db->prepare("UPDATE " . TABLE_THUMBNAILS . " SET file_name = :file_name, file_path = :file_path WHERE article_id = :article_id");
      $stmt->bindValue(":file_name", $newFileName, PDO::PARAM_STR);
      $stmt->bindValue(":file_path", $newFilePath, PDO::PARAM_STR);
      $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);

      return $stmt->execute();
    } catch (PDOException $e) {
      echo "サムネイルパスの更新に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function updateArticleImagePath($id, $newFileUrl)
  {
    try {
      $stmt = $this->db->prepare("
        UPDATE " . TABLE_IMAGES . "
        SET file_url = :file_url WHERE id = :id");
      $stmt->bindValue(":file_url", $newFileUrl, PDO::PARAM_STR);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "サムネイルパスの更新に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function deleteArticleProcess($id)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      $this->deleteArticleImagesAll($id);
      $this->deleteThumbnail($id);
      $this->deleteArticles($id);

      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollBack();
      error_log("記事削除処理中にエラーが発生しました: ", $e->getMessage());
      return false;
    } finally {
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }
  }

  /**
   * 記事を削除
   *
   * @param int $id 削除する記事のID
   * @return bool 成功したときはtrue、失敗したときはfalse
   */
  public function deleteArticles($id)
  {
    try {
      echo $id;
      $stmt = $this->db->prepare("DELETE FROM " . TABLE_ARTICLES . " WHERE id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "記事の削除に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function deleteThumbnail($id)
  {
    try {
      // 削除対象のファイル名を取得
      $stmt = $this->db->prepare("
        SELECT file_name
        FROM " . TABLE_THUMBNAILS . "
        WHERE article_id = :article_id
        ORDER BY upload_date ASC
        LIMIT 1
      ");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      $stmt->execute();
      $file = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($file && isset($file['file_name'])) {
        $filePath = IMAGE_UPLOADS_THUMBNAILS_PATH . $file['file_name'];

        // データベースのレコードを削除
        $stmtDelete = $this->db->prepare("
          DELETE FROM " . TABLE_THUMBNAILS . "
          WHERE article_id = :article_id AND file_name = :file_name
        ");
        $stmtDelete->bindValue(":article_id", $id, PDO::PARAM_INT);
        $stmtDelete->bindValue(":file_name", $file['file_name'], PDO::PARAM_STR);
        $stmtDelete->execute();

        // ファイルの削除
        if (file_exists($filePath)) {
          unlink($filePath);
          echo "filePath: " . $filePath . "\n";
          echo "古いサムネイルを削除しました";
        } else {
          echo "ファイルが見つかりません: " . $filePath;
        }
      } else {
        echo "削除対象のサムネイルが見つかりません";
      }
      return true;
    } catch (PDOException $e) {
      echo "画像の削除に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function deleteArticleImagesAll($id)
  {
    try {
      // 削除対象のファイルパスを取得
      $stmt = $this->db->prepare("
        SELECT file_url
        FROM " . TABLE_IMAGES . "
        WHERE article_id = :article_id
      ");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      $stmt->execute();
      $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // ファイルシステムから削除
      foreach ($files as $file) {
        $filePath =  rtrim(ROOT_PATH, '/') . '/' . ltrim($file['file_url'], '/');

        if (file_exists($filePath)) {
          if (unlink($filePath)) {
            echo "記事に紐付いている画像を削除しました: " . $filePath . "\n";
          }
        } else {
          echo "ファイルが見つかりません: " . $filePath . "\n";
        }
      }

      // データベースのレコードを削除
      $stmt = $this->db->prepare("DELETE FROM " . TABLE_IMAGES . " WHERE article_id = :article_id");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "画像の削除に失敗しました" . $e->getMessage();
      return false;
    }
  }

  /**
   * 記事更新時の画像削除
   *
   * @param int $id  記事番号
   * @param array $deleteImages
   * @return void
   */
  public function deleteArticleImage($id, $deleteImages)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
    try {
      $this->db->beginTransaction();

      // DBの記事番号とファイルパスが一致するレコードが存在するときに削除
      // データベースのレコードを削除
      foreach ($deleteImages as $deleteImage) {
        $stmt = $this->db->prepare("DELETE FROM " . TABLE_IMAGES . " WHERE article_id = :article_id AND file_url = :file_url");
        $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":file_url", $deleteImage, PDO::PARAM_STR);
        $stmt->execute();

        // ファイルシステムから削除
        $filePath =  rtrim(ROOT_PATH, '/') . '/' . ltrim($deleteImage, '/');
        if (file_exists($filePath)) {
          if (unlink($filePath)) {
            echo "記事に紐付いている画像を削除しました: " . $filePath . "\n";
          }
        } else {
          echo "ファイルが見つかりません: " . $filePath . "\n";
        }
      }

      $this->db->commit();
    } catch (PDOException $e) {
      echo "データベースのエラー: " . $e->getMessage();
      $this->db->rollBack();
    } catch (Exception $e) {
      echo "画像の削除に失敗しました: " . $e->getMessage();
      $this->db->rollBack();
    } finally {
      $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }
  }

  public function getAricleWithImagesById($id)
  {
    try {
      // 記事情報の取得
      $article = $this->getArticleById($id);

      // 画像情報の取得
      $thumbnail = $this->getThumbnailById($id);
      if ($article) {
        $article['thumbnailPath'] = $thumbnail['file_path'];
      }

      // 記事の画像の取得
      $articleImagesPath = $this->getArticleImagesById($id);
      if ($articleImagesPath) {
        $article['articleImagesPath'] = $articleImagesPath;
      }

      // echo var_dump($article);

      return $article;
    } catch (Exception $e) {
      echo "データの取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function getArticleById($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT id, title, content, DATE(updated_at) AS formatted_date FROM " . TABLE_ARTICLES . " WHERE id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function getThumbnailById($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT file_name, file_path FROM " . TABLE_THUMBNAILS . " WHERE article_id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "画像の取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function getArticleImagesById($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT file_url, alt_text FROM " . TABLE_IMAGES . " WHERE article_id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "画像の取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function getArticleImageCount($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT COUNT(*) AS image_count FROM " . TABLE_IMAGES . " WHERE article_id = :article_id");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? (int)$result['image_count'] : 0;
    } catch (PDOException $e) {
      return false;
    }
  }

  /**
   * 記事画像のファイル名から連番の次の番号を取得
   *
   * @param [type] $id
   * @return void
   */
  public function getUploadedImageNextNumber($article_id): int
  {
    $number = 1;
    // DBから記事番号の中でIDが一番高いレコードのfile_urlを取得
    try {
      $stmt = $this->db->prepare("
      SELECT file_url
      FROM " . TABLE_IMAGES . "
      WHERE article_id = :article_id
      ORDER BY id DESC
      LIMIT 1
      ");
      $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      echo "result: " . var_dump($result) . PHP_EOL;

      // file_urlの文字列から最初の画像番号と、連番の両方を取り出す
      // 削除した画像があってもその番号は埋めずに、合計何枚目かを元に連番にするため
      if ($result && isset($result['file_url'])) {
        $pattern = "/image_([0-9]+)(_([0-9]+))?\./";
        if (preg_match($pattern, $result['file_url'], $matches)) {
          // 連番部分が存在すれば+1、なければ1を返す
          if (isset($matches[3]) && $matches[3] !== "") {
            $number = $matches[3] + 1;
          } else {
            $number = 1;
          }
        }
      }
      return $number ?? -1;
    } catch (PDOException $e) {
      echo "エラーが発生しました: " . $e;
      return -1;
    }
    return $number ?? -1;
  }

  public function togglePublish($articleId)
  {
    try {
      $stmt = $this->db->prepare("SELECT is_published FROM " . TABLE_ARTICLES .  " WHERE id = :id");
      $stmt->bindValue(":id", $articleId, PDO::PARAM_INT);
      $stmt->execute();
      $article = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($article) {
        if ($article['is_published'] == 1) {
          $newStatus = 0;
        } else {
          $newStatus = 1;
        }
        $updateStmt = $this->db->prepare("UPDATE " . TABLE_ARTICLES . " SET is_published = :status WHERE id = :id");
        $updateStmt->bindValue(':status', $newStatus, PDO::PARAM_INT);
        $updateStmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        return $updateStmt->execute();
      } else {
        return false;
      }
    } catch (PDOException $e) {
      echo "公開ステータスの切り替えに失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function updatePublishDate($articleId, $scheduledPublishDate)
  {
    try {
      $stmt = $this->db->prepare("UPDATE " . TABLE_ARTICLES . " SET scheduled_publish_date = :scheduledPublishDate WHERE id = :id");
      $stmt->bindValue('scheduledPublishDate', $scheduledPublishDate, PDO::PARAM_STR);
      $stmt->bindValue('id', $articleId, PDO::PARAM_INT);
      $stmt->execute();
      return true;
    } catch (PDOException $e) {
      error_log("Error updating publish date: " . $e->getMessage());
      return false;
    }
  }

  public function publishArticle($articleId)
  {
    try {
      $stmt = $this->db->prepare("UPDATE " . TABLE_ARTICLES . " SET is_published = 1 WHERE id = :id");
      $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log("Error publishing articles: " . $e->getMessage());
      return false;
    }
  }

  public function getScheduleArticlesForPublishing($currentDateTime)
  {
    try {
      $stmt = $this->db->prepare("SELECT * FROM " . TABLE_ARTICLES . " WHERE scheduled_publish_date <= :currentDateTime AND is_published = 0");
      $stmt->bindValue(':currentDateTime', $currentDateTime, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error fetching schedulued articles: " . $e->getMessage());
      return [];
    }
  }

  public function getCategoriesList() 
  {
    try {
      $stmt = $this->db->prepare("SELECT name FROM " . TABLE_CATEGORIES);
      $stmt->execute();
      $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $categories;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
    }
  }
}
