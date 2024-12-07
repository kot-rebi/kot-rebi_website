<?php
require_once __DIR__ . '/../../config.php';
require_once CONTROLLERS_PATH . 'BaseArticleController.php';


class CreateArticleController extends BaseArticleController
{

  public function handleRequest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $this->displayCreateForm();
    }

    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if ($this->validateArticleSave($data)) {
      $this->articleModel->insertArticles($data['title'], $data['content']);
      header("Location:" . ADMIN_ARTICLES_URL);
      exit;
    } else {
      echo "入力内容にエラーがありました";
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
