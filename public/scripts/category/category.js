document.getElementById('add-attribute').addEventListener('click', function () {
    let newAttribute = document.createElement('div');
    newAttribute.classList.add('attribute-row', 'mb-2');

    let inputField = document.createElement('input');
    inputField.type = 'text';
    inputField.name = 'attribute_name[]';
    inputField.classList.add('w-full', 'p-2', 'border', 'border-gray-300', 'rounded', 'mt-2');
    inputField.placeholder = 'Назва атрибуту';

    newAttribute.appendChild(inputField);

    document.getElementById('attributes-container').appendChild(newAttribute);
});
