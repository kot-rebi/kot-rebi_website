<?php
require 'vendor/autoload.php';

class CustomParsedown extends Parsedown
{
  public function text($text)
  {
    // 親のメソッドでマークダウンをHTMLに変換
    $html = parent::text($text);

    echo $html;
    $html = $this->wrapParagraphs($html);
    $html = $this->wrapWithDiv($html);
    $html = $this->addClassToH2($html);

    return $html;
  }

  /**
   * <h2>～<h6>タグから次のh系タグまでのコンテンツを囲む
   *
   * @param string $html HTMLに変換された文字列
   * @return void
   */
  private function wrapWithDiv($html)
  {
    $pattern = '/(<h[2-6][^>]*>.*?<\/h[2-6]>)(.*?)(?=<h[2-6]|$)/s';  // h2～h6タグが基準
    $replacement = '<div class="article__content">$1$2</div>';
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

  private function wrapParagraphs($html)
  {
    // 文の中身をマッチさせる
    $html = preg_replace_callback('/([^\n\r]+(\n|\r\n)?)/', function ($matches) {
      $line = trim($matches[0]);

      // 空行の場合は何も返さない
      if ($line === '') {
        return '';
      }

      // 他のHTMLタグがある場合はそのまま返す
      if ($this->hasOtherTags($line)) {
        return $line . "\n";
      }

      // 既に<p></p>で囲まれているときはそのまま返す
      if ($this->isWrappedInPTags($line)) {
        return $line . "\n";
      }

      // <p>タグが一切ついていないときは追加
      if (!$this->hasPTags($line)) {
        return '<p>' . trim($matches[1]) . '</p>' . "\n";
      }

      // <p>のみないときは追加
      if ($this->hasClosingPTag($line)) {
        return '<p>' . trim($matches[1]);
      }
      
      // </p>のみないときに追加
      if (!$this->hasClosingPTag($line)){
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
  private function hasOtherTags($line) {
    // 先頭に<p>
    return substr($line, 0, 1) === '<' && substr($line, 1, 1) !== 'p';
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
  private function hasPTags($line) {
    return strpos($line, '<p>') !== false || strpos($line, '</p>') !== false;
  }

  /**
   * </p>で終わっているかを判定
   *
   * @param string $line
   * @return boolean
   */
  private function hasClosingPTag($line) {
    return substr($line, -4) === '</p>';
  }
}