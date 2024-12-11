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
      $stmt = $this->db->prepare("SELECT * FROM articles LIMIT :limit OFFSET :offset");
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
      SELECT id, title, DATE(updated_at) AS formatted_date
      FROM articles 
      WHERE is_published = 1
      ORDER BY updated_at DESC
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
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM articles");
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
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM articles WHERE is_published = 1");
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
   * @return int|null 成功したときは記事ID、失敗したときはfalse
   */
  public function insertArticles($title, $content, $thumbnailData)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の挿入
      $stmt = $this->db->prepare("INSERT INTO articles (title, content) VALUES (:title, :content)");
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
        INSERT INTO images (article_id, file_name, file_path, alt_text, upload_date)
        VALUES (:article_id, :file_name, :file_path, :alt_text, NOW())
        ");
        $stmtImage->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmtImage->bindValue(":file_name", $thumbnailData['file_name'], PDO::PARAM_STR);
        $stmtImage->bindValue(":file_path", $thumbnailData['file_path'], PDO::PARAM_STR);
        $stmtImage->bindValue(":alt_text", $thumbnailData['alt_text'], PDO::PARAM_STR);
        $stmtImage->execute();
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
  public function updateArticles($id, $title, $content, $thumbnailData = null)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の更新
      $stmt = $this->db->prepare("
        UPDATE articles
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
          INSERT INTO images (article_id, file_name, file_path, alt_text, upload_date)
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
        if (!$this->deleteImage($id)) {
          throw new Exception("古いサムネイル画像の削除に失敗しました");
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
      $stmt = $this->db->prepare("UPDATE images SET file_name = :file_name, file_path = :file_path WHERE article_id = :article_id");
      $stmt->bindValue(":file_name", $newFileName, PDO::PARAM_STR);
      $stmt->bindValue(":file_path", $newFilePath, PDO::PARAM_STR);
      $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);

      return $stmt->execute();
    } catch (PDOException $e) {
      echo "サムネイルパスの更新に失敗しました: " . $e->getMessage();
      return false;
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
      $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "記事の削除に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function deleteImage($id) {
    try {
      // 削除対象のファイル名を取得
      $stmt = $this->db->prepare("
        SELECT file_name
        FROM images
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
          DELETE FROM images
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

  public function getAricleWithImagesById($id)
  {
    try {
      // 記事情報の取得
      $article = $this->getArticleById($id);

      // 画像情報の取得
      $images = $this->getArticleImagesById($id);

      if ($article) {
        $article['thumbnailPath'] = $images['file_path'];
      }

      var_dump($article);

      return $article;
    } catch (Exception $e) {
      echo "データの取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function getArticleById($id)
  {
    try{
      $stmt = $this->db->prepare("SELECT id, title, content, DATE(updated_at) AS formatted_date FROM articles WHERE id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e){
      echo "記事の取得に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function getArticleImagesById($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT file_name, file_path FROM images WHERE article_id = :id");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "画像の取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function togglePublish($articleId)
  {
    try {
      $stmt = $this->db->prepare("SELECT is_published FROM articles WHERE id = :id");
      $stmt->bindValue(":id", $articleId, PDO::PARAM_INT);
      $stmt->execute();
      $article = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($article) {
        if ($article['is_published'] == 1) {
          $newStatus = 0;
        } else {
          $newStatus = 1;
        }
        $updateStmt = $this->db->prepare("UPDATE articles SET is_published = :status WHERE id = :id");
        $updateStmt->bindValue(':status', $newStatus, PDO::PARAM_INT);
        $updateStmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        return $updateStmt->execute();
      } else {
        return false;
      }

    } catch(PDOException $e) {
      echo "公開ステータスの切り替えに失敗しました" . $e->getMessage();
      return false;
    }
  }
}