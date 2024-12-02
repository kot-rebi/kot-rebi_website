<?php
require 'vendor/autoload.php';

class CustomParsedown extends Parsedown
{
  public function text($text)
  {
    // 親のメソッドでマークダウンをHTMLに変換
    $html = parent::text($text);

    // <h2>～<h6>タグから次のh系タグまでのコンテンツを囲む
    $pattern = '/(<h[2-6][^>]*>.*?<\/h[2-6]>)(.*?)(?=<h[2-6]|$)/s';  // h2～h6タグが基準
    $replacement = '<div class="article__content">$1$2</div>';

    return preg_replace($pattern, $replacement, $html);
  }
}