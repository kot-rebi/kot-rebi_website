<!-- フッター -->
<footer class="footer">
  <!-- <a href="#">
    <p>プライバシーポリシー</p>
  </a> -->

  <p class="footer__copyright"><a href="/privacy-policy">プライバシーポリシー</a></p>
  <p class="footer__copyright">&copy;2024-2025 ことれい</p>

  <script src=<?= $config->get('urls')['views_home'] . "/language-label.js" ?>></script>
  <!-- Prism.jsの読み込み -->
  <script src=<?= $config->get('urls')['views_home'] . "/prism.js" ?>></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>

<!-- ★ Prism の再ハイライト処理 -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (window.Prism) {
      Prism.highlightAll();
    }
  });
</script>

  <!-- MathJaxの読み込み -->
  <script>
    window.MathJax = {
      tex: {
        inlineMath: [
          ['$', '$'],
          ['\\(', '\\)']
        ],
        displayMath: [
          ['$$', '$$'],
          ['\\[', '\\]']
        ],
      },
      svg: {
        fontCache: 'global'
      }
    };
  </script>


  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
</footer>