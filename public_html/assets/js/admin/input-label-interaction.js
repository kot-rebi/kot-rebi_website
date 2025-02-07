function changeLabelColor(element, label, color) {
  element.addEventListener('focus', function () {
    label.style.color = color;
  });

  element.addEventListener('blur', function() {
    label.style.color = '';
  });
}

const inputElement = document.querySelector('.admin-create__title input');
const titleInputLabel = document.querySelector('label[for="title"]');
changeLabelColor(inputElement, titleInputLabel, '#FBAA31');

const contentTextareaElement = document.querySelector('.admin-create__content textarea');
const articleContentTextareaLabel = document.querySelector('label[for="article-content"]');
changeLabelColor(contentTextareaElement, articleContentTextareaLabel, '#FBAA31');