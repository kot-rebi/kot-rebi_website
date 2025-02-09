document.querySelectorAll('.code-block').forEach(block => {
  const codeElement = block.querySelector('code');
  // 言語抽出
  const languageClass = codeElement.className.split(' ').find(className => className.startsWith('language-'));
  const language = languageClass.replace('language-', '');

  const label = document.createElement('div');
  label.className = 'language-label';
  label.textContent = language.charAt(0).toUpperCase() + language.slice(1);
  block.insertBefore(label, block.firstChild);
})