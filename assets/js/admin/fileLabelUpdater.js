document.querySelectorAll('input[type="file"]').forEach((input) => {
  const label = input.previousElementSibling;
  input.addEventListener('change', (event) => {
    const fileName = event.target.files[0]?.name || "ファイルを選択";
    label.textContent = fileName;
  });
});