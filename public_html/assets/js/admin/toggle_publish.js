function togglePublish(articleId) {
  const checkbox = document.querySelector(`input[data-checkbox-id="${articleId}"]`);
  const checkedStatus = checkbox.checked;

  if (confirm("公開ステータスを変更してもよろしいですか？")) {
    fetch("/api/admin/toggle_status.php?id=" + articleId)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log(data.message);
          location.reload;
        } else {
          alert('エラー: ' + data.error);
        }
      })
      .catch(error => {
        alert("通信エラーが発生しました: " + error.message);
      });
  } else {
    checkbox.checked = !checkedStatus;
  }
}