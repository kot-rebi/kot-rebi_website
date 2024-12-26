document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('image-input-container');
  const addButton = document.getElementById('add-image-button');

  addButton.addEventListener('click', () => {
    // 画像と説明テキストのセットを生成
    const newInputSet = document.createElement('div');
    newInputSet.classList.add('image-input-set');

    // 画像
    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.name = 'images[]';
    imageInput.accept = 'image/*';
    imageInput.required = true;

    // 説明
    const altTextInput = document.createElement('input');
    altTextInput.type = 'text';
    altTextInput.name = 'alt_texts[]';
    altTextInput.placeholder = '画像の説明を入力';

    // 削除
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.textContent = '削除';
    removeButton.addEventListener('click', () => {
      container.removeChild(newInputSet);
    });

    // 作成したセットを追加
    newInputSet.appendChild(imageInput);
    newInputSet.appendChild(altTextInput);
    newInputSet.appendChild(removeButton);

    // コンテナに追加
    container.appendChild(newInputSet);
  })
})