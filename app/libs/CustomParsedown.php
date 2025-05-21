<?php

class CustomParsedown extends Parsedown
{
  protected $mathBlocks = [];

  public function text($text)
  {
    // 数式部分を一時的に置換（ブロック式 $$...$$）
    $text = preg_replace_callback('/\$\$(.*?)\$\$/s', function ($matches) {
      $key = 'MATHBLOCK_' . count($this->mathBlocks);
      $this->mathBlocks[$key] = $matches[0];
      return $key;
    }, $text);

    // 親のメソッドでマークダウンをHTMLに変換
    $html = parent::text($text);

    // MathBlockを復元する
    $html = $this->restoreMathBlocks($html);

    $html = $this->wrapParagraphs($html);
    $html = $this->wrapHeadingWithDiv($html);
    $html = $this->wrapCodeWithDiv($html);
    $html = $this->addClassToH2($html);
    $html = $this->addClassToH3($html);
    $html = $this->addClassToPre($html);

    return $html;
  }

  private function restoreMathBlocks(string $html)
  {
    $keys = array_keys($this->mathBlocks);
    usort($keys, function ($a, $b) {
      return strlen($b) - strlen($a);
    });

    foreach ($keys as $key) {
      $html = str_replace($key, $this->mathBlocks[$key], $html);
    }
    return $html;
  }

  /**
   * <h2>～<h6>タグから次のh系タグまでのコンテンツを囲む
   *
   * @param string $html HTMLに変換された文字列
   * @return void
   */
  private function wrapHeadingWithDiv($html)
  {
    $pattern = '/(<h[2-6][^>]*>.*?<\/h[2-6]>)(.*?)(?=<h[2-6]|$)/s';  // h2～h6タグが基準
    $replacement = '<div class="article__content">$1$2</div>';
    return preg_replace($pattern, $replacement, $html);
  }

  /**
   * <pre>～</pre>のコードブロック間を<div>で囲む
   *
   * @param string $html HTMLに変換された文字列
   * @return void
   */
  private function wrapCodeWithDiv($html)
  {
    $pattern = '/(<pre[^>]*>.*?<\/pre>)/s';
    $replacement = '<div class="code-block">$1</div>';
    return preg_replace($pattern, $replacement, $html);
  }

  /**
   * h2タグにクラスをつける
   *
   * @param string $html HTMLに変換された文字列
   * @return void
   */
  private function addClassToH2($html)
  {
    $pattern = '/(<h2)([^>]*>)/';
    $replacement = '$1 class="underline-with-background"$2';
    return preg_replace($pattern, $replacement, $html);
  }

  /**
   * h3タグにクラスをつける
   *
   * @param string $html HTMLに変換された文字列
   * @return void
   */
  private function addClassToH3($html)
  {
    $pattern = '/(<h3)([^>]*>)/';
    $replacement = '$1 class="orange-circle"$2';
    return preg_replace($pattern, $replacement, $html);
  }

  private function addClassToPre($html)
  {
    $pattern = '/(<pre)([^>]*>)/';
    $replacement = '$1 class="line-numbers" data-copy="true"$2';
    return preg_replace($pattern, $replacement, $html);
  }

  /**
   * 段落内を<p>タグで囲む
   *
   * @param string $html
   * @return void
   */
  private function wrapParagraphs($html)
  {
    // 文の中身をマッチさせる
    $html = preg_replace_callback('/([^\n\r]+(\n|\r\n)?)/', function ($matches) use (&$isInsidePre, &$isInsideMath) {
      $line = $matches[0];

      // 空行の場合は何も返さない
      if ($line === '') {
        return '';
      }

      // <pre>タグ内でのみ<p>タグはつけない
      $preHandled = $this->handlePreTags($line, $isInsidePre);
      if ($preHandled !== null) {
        return $preHandled;
      }

      // 数式ブロック中は<p>タグをつけない
      $mathHandled = $this->handleMathJax($line, $isInsideMath);
      if ($mathHandled !== null) {
        return $mathHandled;
      }

      // 他のHTMLタグがある場合はそのまま返す
      if ($this->hasOtherTags($line)) {
        return $line . "\n";
      }

      // 既に<p></p>で囲まれているときはそのまま返す
      if ($this->isWrappedInPTags($line)) {
        return $line . "\n";
      }

      // <p>タグが一切ついていないときは両方追加
      if (!$this->hasPTags($line)) {
        return '<p>' . trim($matches[1]) . '</p>' . "\n";
      }

      // <p>のみないときは追加
      if ($this->hasClosingPTag($line)) {
        return '<p>' . trim($matches[1]);
      }

      // </p>のみないときは追加
      if (!$this->hasClosingPTag($line)) {
        return trim($matches[1]) . '</p>' . "\n";
      }

      return $line;
    }, $html);

    return $html;
  }

  /**
   * 先頭に<p>タグ以外のタグがあるか判定
   *
   * @param string $line 
   * @return boolean
   */
  private function hasOtherTags($line)
  {
    // 先頭に<p>
    return substr($line, 0, 1) === '<' && substr($line, 1, 1) !== 'p';
  }

  /**
   * <pre>タグ内部の処理
   *
   * @param string $line
   * @param boolean $isInsidePre  <pre>タグ内部かどうか
   * @return boolean
   */
  private function handlePreTags($line, &$isInsidePre)
  {
    // <pre>タグの開始を検知
    if (strpos($line, '<pre') !== false) {
      $isInsidePre = true;
      return $line;
    }

    // <pre>タグの終了を検知
    if (strpos($line, '</pre>') !== false) {
      $isInsidePre = false;
      return $line;
    }

    if ($isInsidePre) {
      return $line;
    }

    return null;
  }

  /**
   * <p>タグで囲まれているか判定
   *
   * @param string $line
   * @return boolean
   */
  private function isWrappedInPTags($line)
  {
    return preg_match('/^<p>.*<\/p>$/s', $line);
  }

  /**
   * <p>と</p>の両方があるか判定
   *
   * @param string $line
   * @return boolean
   */
  private function hasPTags($line)
  {
    return strpos($line, '<p>') !== false || strpos($line, '</p>') !== false;
  }

  /**
   * </p>で終わっているかを判定
   *
   * @param string $line
   * @return boolean
   */
  private function hasClosingPTag($line)
  {
    return substr(rtrim($line), -4) === '</p>';
  }

  /**
   * 数式ブロック中の処理
   *
   * @param string $line
   * @param boolean $isInsideMath 数式ブロック中かどうか
   * @return void
   */
  private function handleMathJax($line, &$isInsideMath)
  {
    $stripped = trim(strip_tags($line));

    // 一行完結の場合（$$ ... $$）
    if (preg_match('/^\$\$(.*?)\$\$$/s', $line)) {
      return $line;
    }

    // ブロック式の開始行
    if ($stripped === '$$') {
      $isInsideMath = !$isInsideMath;
      return "$$";
    }

    // 数式ブロック中はそのまま返す
    if ($isInsideMath) {
      return $line . "\n";
    }

    return null;
  }
}
