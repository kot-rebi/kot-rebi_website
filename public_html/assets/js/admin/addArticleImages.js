document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('image-input-container');
  const addButton = document.getElementById('add-image-button');

  addButton.addEventListener('click', () => {

    const uniqueId = `image-${Date.now()}`;

    // 画像と説明テキストのセットを生成
    const newInputSet = document.createElement('div');
    newInputSet.classList.add('image-input-set');

    // ラベル
    const customFileLabel = document.createElement('label');
    customFileLabel.htmlFor = uniqueId;
    customFileLabel.classList.add('file-input-label');
    customFileLabel.textContent = "画像を選択";

    // 画像
    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.classList.add('hidden-file-input');
    imageInput.id = uniqueId;
    imageInput.name = 'images[]';
    imageInput.accept = 'image/*';
    imageInput.required = true;

    // 説明
    const altTextInput = document.createElement('input');
    altTextInput.type = 'text';
    altTextInput.id =  'alt_text';
    altTextInput.name = 'alt_texts[]';
    altTextInput.placeholder = 'altテキスト';

    // 削除
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.textContent = '削除';
    removeButton.classList.add('imageset-delete-button');
    removeButton.addEventListener('click', () => {
      container.removeChild(newInputSet);
    });

    // 追加されたinputにファイル名を表示するイベントリスナーを設定
    imageInput.addEventListener('change', (event) => {
      const fileName = event.target.files[0]?.name || "画像を選択";
      customFileLabel.textContent = fileName;
    })

    // 作成したセットを追加
    newInputSet.appendChild(customFileLabel);
    newInputSet.appendChild(imageInput);
    newInputSet.appendChild(altTextInput);
    newInputSet.appendChild(removeButton);

    // コンテナに追加
    container.appendChild(newInputSet);
  })
})