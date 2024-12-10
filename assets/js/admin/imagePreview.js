document.addEventListener('DOMContentLoaded', function() {
  const preview = document.getElementById('thumbnailPreview');

  if (preview.src !== "") {
    preview.style.display = 'block';
  }
})

// ファイル選択時の処理
document.getElementById('thumbnail').addEventListener('change', function(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('thumbnailPreview');
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});