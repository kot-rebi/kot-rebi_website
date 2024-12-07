<?php
require_once __DIR__ . '/../../config.php';
require_once CONTROLLERS_PATH . 'BaseArticleController.php';

class EditArticleController extends BaseArticleController
{


  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->displayEditForm();
    }

    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
      $article = $this->articleModel->getArticleById($id);
      $this->setArticleVariables($article);

      // ビューに渡す配列データ
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

    if ($this->validateArticleId($id)) {
      if ($this->validateArticleSave($data)) {
        $this->articleModel->updateArticles($id, $data['title'], $data['content']);
        echo "記事を更新しました";
        header("Location: " . ADMIN_ARTICLES_URL);
        exit;
      } else {
        echo "エラー： 記事の保存に失敗しました";
      }
    } else {
      echo "エラー： 記事IDの取得に失敗しました";
    }
  }

  /**
   * 編集用のviewの変数をセットする
   *
   * @param array $article DBに保存されている記事情報
   *  - string $article['id'] 記事のID
   *  - string $article['title'] 記事のタイトル
   *  - string $article['content'] 記事の本文
   * @return void
   */
  private function setArticleVariables($article)
  {
    $this->formTitle = '編集';
    $this->formAction = ADMIN_ARTICLES_URL . '/edit?id=' . $article['id'];
    $this->articleTitle = $article['title'];
    $this->articleContent = $article['content'];
    $this->submitLabel = '更新';
    $this->articleId = $article['id'];
  }
}