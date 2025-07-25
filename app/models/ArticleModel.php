<?php

/**
 * 記事に関するデータ操作を管理するクラス
 */
class ArticleModel
{
  private $db;
  private $config;

  /**
   * コンストラクタ
   * Database クラスからデータベース接続を受け取る
   */
  public function __construct()
  {
    $this->config = Config::getInstance();
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
      $stmt = $this->db->prepare("
        SELECT 
          a.*, 
          c.name AS category_name, 
          DATE_FORMAT(a.created_at, '%Y年%c月%e日') AS formatted_created_at, 
          DATE_FORMAT(a.updated_at, '%Y年%c月%e日') AS formatted_updated_at 
        FROM " . $this->config->get('tables')['articles'] . " AS a 
        LEFT JOIN " . $this->config->get('tables')['categories'] . " AS c 
        ON a.category_id = c.id 
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
   * 指定されたカテゴリーの記事を取得する
   *
   * @param int $categoryId カテゴリーID
   * @param int $limit 取得件数
   * @param int $offset 何件目から取得するか
   * @return void
   */
  public function getArticlesByCategory($categoryId, $limit, $offset, $excludeArticleId = null)
  {
    try {
      $query = "
        SELECT 
        a.id, 
        a.title,
        a.slug, 
        DATE(a.scheduled_publish_date) AS formatted_date,
        i.file_path AS thumbnail_path,
        c.slug AS category_slug,
        c.name AS category_name
      FROM " . $this->config->get('tables')['articles'] . " a
      LEFT JOIN " . $this->config->get('tables')['thumbnails'] . " i ON a.id = i.article_id
      LEFT JOIN " . $this->config->get('tables')['categories'] . " c ON a.category_id = c.id
      WHERE a.is_published = 1
      AND a.category_id = :category_id";

      if ( $excludeArticleId !== null)
      {
        $query .= " AND a.id != :exclude_id";
      }

      $query .= " ORDER BY a.scheduled_publish_date DESC
      LIMIT :limit OFFSET :offset";
      
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      if ($excludeArticleId !== null) {
        $stmt->bindValue(":exclude_id", $excludeArticleId, PDO::PARAM_INT);
      }
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "データの取得に失敗しました: " . $e->getMessage();
      return [];
    }
  }

  public function getTotalArticlesByCategory($categoryId)
  {
    try {
      $stmt = $this->db->prepare(
        "SELECT COUNT(*) FROM " . $this->config->get('tables')['articles'] . "
      WHERE category_id = :category_id
      AND is_published = 1"
      );
      $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      echo $e->getMessage();
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
        c.slug AS category_name,
        a.slug,
        DATE(a.scheduled_publish_date) AS formatted_date,
        i.file_path AS thumbnail_path
      FROM " . $this->config->get('tables')['articles'] . " a
      LEFT JOIN " . $this->config->get('tables')['thumbnails'] . " i ON a.id = i.article_id
      LEFT JOIN " . $this->config->get('tables')['categories'] . " c ON a.category_id = c.id
      WHERE a.is_published = 1
      ORDER BY a.scheduled_publish_date DESC
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
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM " . $this->config->get('tables')['articles']);
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
      $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM " . $this->config->get('tables')['articles'] . " WHERE is_published = 1");
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['total'];
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました: " . $e->getMessage();
      return 0;
    }
  }

  /**
   * ゲームのデータを取得する
   *
   * @return void
   */
  public function getGames()
  {
    try {
      $stmt = $this->db->prepare("SELECT title, thumbnail_path, description, url, published_at 
      FROM " . $this->config->get('tables')['games']);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "ゲームの取得に失敗しました: " . $e->getMessage();
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
  public function insertArticles($title, $content, $thumbnailData, &$imageData, $categoryId, $metaTag)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の挿入
      $stmt = $this->db->prepare("INSERT INTO " . $this->config->get('tables')['articles'] . " (title, content, category_id, meta_tag) VALUES (:title, :content, :category_id, :meta_tag)");
      $stmt->bindValue(":title", $title, PDO::PARAM_STR);
      $stmt->bindValue(":content", $content, PDO::PARAM_STR);
      $stmt->bindValue(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindValue(":meta_tag", $metaTag, PDO::PARAM_STR);
      $stmt->execute();

      $articleId = $this->db->lastInsertId();
      if (!$articleId) {
        throw new Exception("記事IDの取得に失敗しました");
      }

      // サムネイル画像はimagesテーブルに保存
      if ($thumbnailData) {
        $stmtImage = $this->db->prepare("
        INSERT INTO " . $this->config->get('tables')['thumbnails'] . " (article_id, file_name, file_path, alt_text, upload_date)
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
        INSERT INTO " . $this->config->get('tables')['images'] . " (article_id, file_url, alt_text, uploaded_at)
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
  public function updateArticles($id, $title, $content, $thumbnailData = null, &$imageData, $categoryId, $metaTag)
  {
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

    try {
      $this->db->beginTransaction();

      // 記事の更新
      $stmt = $this->db->prepare("
        UPDATE " . $this->config->get('tables')['articles'] . "
        SET title = :title, content = :content, updated_at = NOW(), category_id = :category_id, meta_tag = :meta_tag
        WHERE id = :id
      ");
      $stmt->bindValue(":title", $title, PDO::PARAM_STR);
      $stmt->bindValue(":content", $content, PDO::PARAM_STR);
      $stmt->bindValue(":category_id", $categoryId, PDO::PARAM_INT);
      $stmt->bindValue(":meta_tag", $metaTag, PDO::PARAM_STR);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      if (!$stmt->execute()) {
        throw new Exception("記事の更新に失敗しました");
      }

      // サムネイル画像の更新
      if ($thumbnailData) {
        // 新しいサムネイルを挿入
        $stmtImage = $this->db->prepare("
          INSERT INTO " . $this->config->get('tables')['thumbnails'] . " (article_id, file_name, file_path, alt_text, upload_date)
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
        INSERT INTO " . $this->config->get('tables')['images'] . " (article_id, file_url, alt_text, uploaded_at)
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
      $stmt = $this->db->prepare("UPDATE " . $this->config->get('tables')['thumbnails'] . " SET file_name = :file_name, file_path = :file_path WHERE article_id = :article_id");
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
        UPDATE " . $this->config->get('tables')['images'] . "
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
      $stmt = $this->db->prepare("DELETE FROM " . $this->config->get('tables')['articles'] . " WHERE id = :id");
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
        FROM " . $this->config->get('tables')['thumbnails'] . "
        WHERE article_id = :article_id
        ORDER BY upload_date ASC
        LIMIT 1
      ");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      $stmt->execute();
      $file = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($file && isset($file['file_name'])) {
        $filePath = $this->config->get('assets')['thumbnails'] . '/' . $file['file_name'];

        // データベースのレコードを削除
        $stmtDelete = $this->db->prepare("
          DELETE FROM " . $this->config->get('tables')['thumbnails'] . "
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
        FROM " . $this->config->get('tables')['images'] . "
        WHERE article_id = :article_id
      ");
      $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
      $stmt->execute();
      $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // ファイルシステムから削除
      foreach ($files as $file) {
        $filePath =  rtrim($this->config->get('ROOT_PATH'), '/') . '/' . ltrim($file['file_url'], '/');

        if (file_exists($filePath)) {
          if (unlink($filePath)) {
            echo "記事に紐付いている画像を削除しました: " . $filePath . "\n";
          }
        } else {
          echo "ファイルが見つかりません: " . $filePath . "\n";
        }
      }

      // データベースのレコードを削除
      $stmt = $this->db->prepare("DELETE FROM " . $this->config->get('tables')['images'] . " WHERE article_id = :article_id");
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
        $stmt = $this->db->prepare("DELETE FROM " . $this->config->get('tables')['images'] . " WHERE article_id = :article_id AND file_url = :file_url");
        $stmt->bindValue(":article_id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":file_url", $deleteImage, PDO::PARAM_STR);
        $stmt->execute();

        // ファイルシステムから削除
        $filePath =  rtrim($this->config->get('ROOT_PATH'), '/') . '/' . ltrim($deleteImage, '/');
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

  /**
   * 記事番号に対応する記事を取得
   * サムネイル画像も取得
   * 
   * @param int $id 記事番号
   * @return void
   */
  public function getArticleById($id)
  {
    try {
      $stmt = $this->db->prepare("
      SELECT
        a.id, 
        a.title, 
        a.content,
        a.category_id,
        a.meta_tag,
        DATE(a.scheduled_publish_date) AS formatted_date,
        i.file_path AS thumbnail_path
      FROM " . $this->config->get('tables')['articles'] . " a
      LEFT JOIN " . $this->config->get('tables')['thumbnails'] . " i ON a.id = i.article_id 
      WHERE a.id = :id
      ");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function getArticleByCategorySlugAndArticleSlug($categorySlug, $articleSlug)
  {
    try {
      $stmt = $this->db->prepare("
      SELECT
        a.id,
        a.title,
        a.content,
        a.category_id,
        a.meta_tag,
        DATE(a.scheduled_publish_date) AS formatted_date,
        i.file_path AS thumbnail_path
      FROM " . $this->config->get('tables')['articles'] . " a
      LEFT JOIN " . $this->config->get('tables')['thumbnails'] . " i ON a.id = i.article_id 
      LEFT JOIN " . $this->config->get('tables')['categories'] . " c ON a.category_id = c.id
      WHERE c.slug = :categorySlug AND a.slug = :articleSlug
      ");
      $stmt->bindValue(":categorySlug", $categorySlug, PDO::PARAM_STR);
      $stmt->bindValue(":articleSlug", $articleSlug, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "記事の取得に失敗しました" . $e->getMessage();
      return false;
    }
  }

  public function getSlugsByArticleId($id)
  {
    try {
      $stmt = $this->db->prepare("
      SELECT
        a.slug AS article_slug,
        c.slug AS category_slug
      FROM " . $this->config->get('tables')['articles'] . " a 
      INNER JOIN " . $this->config->get('tables')['categories'] . " c 
      ON a.category_id = c.id 
      WHERE a.id = :id  
      ");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "スラッグの取得に失敗しました: " . $e->getMessage();
      return false;
    }
  }

  public function getThumbnailById($id)
  {
    try {
      $stmt = $this->db->prepare("SELECT file_name, file_path FROM " . $this->config->get('tables')['thumbnails'] . " WHERE article_id = :id");
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
      $stmt = $this->db->prepare("SELECT file_url, alt_text FROM " . $this->config->get('tables')['images'] . " WHERE article_id = :id");
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
      $stmt = $this->db->prepare("SELECT COUNT(*) AS image_count FROM " . $this->config->get('tables')['images'] . " WHERE article_id = :article_id");
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
      FROM " . $this->config->get('tables')['images'] . "
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
      $stmt = $this->db->prepare("SELECT is_published FROM " . $this->config->get('tables')['articles'] .  " WHERE id = :id");
      $stmt->bindValue(":id", $articleId, PDO::PARAM_INT);
      $stmt->execute();
      $article = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($article) {
        if ($article['is_published'] == 1) {
          $newStatus = 0;
        } else {
          $newStatus = 1;
        }
        $updateStmt = $this->db->prepare("UPDATE " . $this->config->get('tables')['articles'] . " SET is_published = :status WHERE id = :id");
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
      $stmt = $this->db->prepare("UPDATE " . $this->config->get('tables')['articles'] . " SET scheduled_publish_date = :scheduledPublishDate WHERE id = :id");
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
      $stmt = $this->db->prepare("UPDATE " . $this->config->get('tables')['articles'] . " SET is_published = 1 WHERE id = :id");
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
      $stmt = $this->db->prepare("SELECT * FROM " . $this->config->get('tables')['articles'] . " WHERE scheduled_publish_date <= :currentDateTime AND is_published = 0");
      $stmt->bindValue(':currentDateTime', $currentDateTime, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error fetching schedulued articles: " . $e->getMessage());
      return [];
    }
  }

  public function articleExists($id)
  {
    try {
      $stmt = $this->db->prepare("
        SELECT COUNT(*) FROM " . $this->config->get('tables')['articles'] . " WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
      echo "記事が存在しません";
      return;
    }
  }

  public function saveDailyViews($articleId, $views, $viewDate)
  {
    try {
      $stmt = $this->db->prepare("
      INSERT INTO " . $this->config->get('tables')['dailyViews'] . "(article_id, views, view_date) 
      VALUES (:article_id, :views, :view_date)
      ON DUPLICATE KEY UPDATE views = :update_views
      ");
      $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
      $stmt->bindValue(':views', $views, PDO::PARAM_INT);
      $stmt->bindValue(':view_date', $viewDate, PDO::PARAM_STR);
      $stmt->bindValue(':update_views', $views, PDO::PARAM_INT);
      return $stmt->execute();
    } catch(PDOException $e) {
      echo "保存できませんでした";
      return;
    }
  }

  // ========================================
  // カテゴリー一覧
  // ========================================

  /**
   * カテゴリー一覧を取得する
   *
   * @return void
   */
  public function getCategoriesList()
  {
    try {
      $stmt = $this->db->prepare("SELECT id, name, slug FROM " . $this->config->get('tables')['categories']);
      $stmt->execute();
      $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $categories;
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
    }
  }

  /**
   * 記事のカテゴリーを更新する
   *
   * @param int $articleId 記事ID
   * @param int $categoryId カテゴリーID
   * @return void
   */
  public function updateArticleCategory($articleId, $categoryId)
  {
    try {
      $stmt = $this->db->prepare("
        UPDATE " . $this->config->get('tables')['articles'] . " 
        SET category_id = :category_id
        WHERE id = :article_id
      ");
      $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
      $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return false;
    }
  }

  public function getCategoryById($id)
  {
    try {
      $stmt = $this->db->prepare(" 
        SELECT id, name, slug 
        FROM " . $this->config->get('tables')['categories'] . " 
        WHERE id = :id
      ");
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return false;
    }
  }

  /**
   * スラッグからカテゴリ情報を取得する
   *
   * @param string $slug カテゴリのスラッグ名
   * @return void カテゴリに関するデータ
   */
  public function getCategoryBySlug($slug)
  {
    try {
      $stmt = $this->db->prepare("
        SELECT * FROM " . $this->config->get('tables')['categories'] . " 
        WHERE slug = :slug LIMIT 1
      ");
      $stmt->bindValue(":slug", $slug, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return false;
    }
  }

  // ========================================
  // 人気記事
  // ========================================

  public function getPopularArticles($limit = 3)
  {
    try {
      $stmt = $this->db->prepare("
        SELECT 
          v.article_id,
          a.title,
          a.slug,
          c.slug AS category_name,
          i.file_path AS thumbnail_path,
          SUM(v.views) AS total_views 
        FROM " . $this->config->get('tables')['dailyViews'] . " v 
        LEFT JOIN " .  $this->config->get('tables')['articles'] . " a ON v.article_id = a.id 
        LEFT JOIN " . $this->config->get('tables')['thumbnails'] . " i ON v.article_id = i.article_id 
        LEFT JOIN " . $this->config->get('tables')['categories'] . " c ON a.category_id = c.id 
        GROUP BY v.article_id 
        ORDER BY total_views DESC 
        LIMIT :limit"
      );
      $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "取得に失敗しました" . $e->getMessage();
      return false;
    }
  }
}
