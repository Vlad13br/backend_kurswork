function fetchAttributes(categoryId) {
    fetch(`/get-category-attributes?category_id=` + categoryId)
        .then(response => response.json())
        .then(data => {
            let attributesDiv = document.getElementById('attributes');
            attributesDiv.innerHTML = '';

            if (data.length > 0) {
                data.forEach(attribute => {
                    let input = `<label class='block mb-2'>${attribute}:
                <input type="text" name="attributes[${attribute}]" required class='border p-2 w-full rounded'></label>`;
                    attributesDiv.innerHTML += input;
                });
            } else {
                attributesDiv.innerHTML = `<p>Атрибути не знайдені для цієї категорії.</p>`;
            }

        })
        .catch(error => {
            console.error("Помилка при отриманні атрибутів:", error);
        });
}


function previewImages(event) {
    let previewContainer = document.getElementById('image-preview');
    previewContainer.innerHTML = '';
    let files = event.target.files;

    Array.from(files).forEach((file, index) => {
        let reader = new FileReader();
        reader.onload = function(e) {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('w-24', 'h-24', 'object-cover', 'cursor-pointer', 'border', 'border-gray-300', 'rounded');

            let selectButton = document.createElement('button');
            selectButton.classList.add('mt-2', 'bg-blue-500', 'text-white', 'px-2', 'py-1', 'rounded');
            selectButton.textContent = 'Основне';
            selectButton.onclick = () => setMainImage(index, img);

            let div = document.createElement('div');
            div.appendChild(img);
            div.appendChild(selectButton);
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function setMainImage(index, imgElement) {
    let previousMainImage = document.querySelector('.main-image');
    if (previousMainImage) {
        previousMainImage.classList.remove('main-image', 'border-4', 'border-green-500', 'shadow-lg');
    }

    imgElement.classList.add('main-image', 'border-4', 'border-green-500', 'shadow-lg');
    document.getElementById('main_image').value = index;
}
