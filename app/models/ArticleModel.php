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
    $this->db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
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
      return $articleId;
    } catch (PDOException $e) {
      $this->db->rollBack();
      echo "記事の挿入に失敗しました: " . $e->getMessage();
      return false;
    } catch (Exception $e) {
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
  public function updateArticles($id, $title, $content)
  {
    try {
      $stmt = $this->db->prepare("UPDATE articles SET title = :title, content = :content, updated_at = NOW() WHERE id = :id");
      $stmt->bindValue(":title", $title, PDO::PARAM_STR);
      $stmt->bindValue(":content", $content, PDO::PARAM_STR);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "記事の更新に失敗しました: " . $e->getMessage();
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