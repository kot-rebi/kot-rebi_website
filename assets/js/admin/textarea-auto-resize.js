const textareaElement = document.querySelector('#article-content');
textareaElement.setAttribute("style", `height: ${textareaElement.scrollHeight}px;`);

let isUserScrolledUp = false;
textareaElement.addEventListener('focus', function() {
  if (this.scrollTop < this.scrollHeight - this.clientHeight) {
    isUserScrolledUp = true;
  } else {
    isUserScrolledUp = false;
  }
})

function setTextareaHeight() {
  this.style.height = "auto";
  this.style.height = `${this.scrollHeight}px`;

  if (!isUserScrolledUp) {
    this.scrollTop = this.scrollHeight;
  }
}

textareaElement.addEventListener("input", setTextareaHeight);
setTextareaHeight.call(textareaElement);