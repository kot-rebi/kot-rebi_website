document.addEventListener('DOMContentLoaded', function() {
  const currentThumbnail = document.getElementById('thumbnailPreview');
  const newThumbnailPreview = document.getElementById('newThumbnailPreview');

  // 現在のサムネイルが表示されているときは表示
  if (currentThumbnail.src !== "" && !currentThumbnail.classList.contains('admin-create__thumbnailHidden')) {
    currentThumbnail.classList.remove('admin-create__thumbnailHidden');
  }
})

// ファイル選択時の処理
document.getElementById('thumbnail').addEventListener('change', function(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      newThumbnailPreview.src = e.target.result;
      newThumbnailPreview.classList.remove('admin-create__thumbnailHidden');
    };
    reader.readAsDataURL(file);
  }
});