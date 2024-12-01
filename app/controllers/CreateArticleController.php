<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/controllers/BaseArticleController.php';


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
    
    include $_SERVER['DOCUMENT_ROOT'] . '/pj_homepage/app/views/admin/createArticle.php';
  }

  private function insertArticle()
  {
    $data = $this->getInputData();

    if ($this->validateArticleSave($data)) {
      $this->articleModel->insertArticles($data['title'], $data['content']);
      header("Location: /pj_homepage/admin/articles");
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
    $this->formAction = '/pj_homepage/admin/articles/create';
    $this->articleTitle = '';
    $this->articleContent = '';
    $this->submitLabel = '送信';
    $this->articleId = '';
  }
}
