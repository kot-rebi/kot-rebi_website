document.addEventListener('DOMContentLoaded', () => {
  const copyButtons = document.querySelectorAll('.copy-button');

  copyButtons.forEach(button => {
    button.addEventListener('click', () => {
      
      const targetId = button.getAttribute('data-target');              // data-target属性からIDを取得
      const textToCopy = document.getElementById(targetId)?.innerText;  // コピー対象のテキスト

      if (textToCopy) {
        navigator.clipboard.writeText(textToCopy)
        .then(() => {
          alert('クリップボードにコピーしました！');
        })
        .catch(error => {
          console.error('コピーに失敗しました:  ' , error);
        });
      } else {
        alert('コピー対象のテキストが見つかりませんでした');
      }
    })
  })
})