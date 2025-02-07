function togglePublish(articleId) {
  const checkbox = document.querySelector(`input[data-checkbox-id="${articleId}"]`);
  const checkedStatus = checkbox.checked;

  if (confirm("公開ステータスを変更してもよろしいですか？")) {
    console.log("変更します");
    fetch(CONTROLLERS_PATH + "/toggle_status.php?id=" + articleId)
      .then(response => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error("サーバーエラーが発生しました");
        }
      })
      .then(data => {
        console.log(data);
        location.reload();
      })
      .catch(error => {
        alert("エラーが発生しました: " + error.message);
      });
  } else {
    checkbox.checked = !checkedStatus;
    return;
  }
}