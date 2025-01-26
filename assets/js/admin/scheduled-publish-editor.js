function toggleScheduledPublishEdit(id) {
  const editForm = document.getElementById(`edit-form-${id}`);
  const isHidden = editForm.style.display === "none";
  editForm.style.display = isHidden ? "block" : "none";
}