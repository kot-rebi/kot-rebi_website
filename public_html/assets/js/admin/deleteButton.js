function confirmDeleteButton() {
  try {
    return confirm("本当に削除しますか？");
  } catch (error) {
    console.log("エラーが発生しました: ", error);
    alert("問題が発生しました。もう一度お試しください(-_-)");
  }
}