document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filter-form');
    const sortForm = document.getElementById('sort-form');
    const resetForm = document.getElementById('reset-form');
    const productList = document.getElementById('product-list');

    function applyFilter() {
        const filterData = new FormData(filterForm);
        const sortData = new FormData(sortForm);

        const params = new URLSearchParams(filterData);
        for (const [key, value] of sortData.entries()) {
            params.set(key, value);
        }

        fetch(`/fetch?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                renderProducts(data.products);
            })
            .catch(error => {
                console.error('Помилка завантаження товарів:', error);
                productList.innerHTML = '<p class="text-center text-red-500">Не вдалося завантажити товари.</p>';
            });
    }

    function renderProducts(products) {
        if (!products.length) {
            productList.innerHTML = '<p class="text-center text-gray-600">Товарів не знайдено за заданими критеріями.</p>';
            return;
        }

        const fragment = document.createDocumentFragment();

        products.forEach(product => {
            const outOfStock = product.product_stock <= 0;
            const hasDiscount = product.product_discount && product.product_discount > 0;
            const finalPrice = hasDiscount
                ? (product.product_price * (1 - product.product_discount / 100)).toFixed(2)
                : product.product_price;

            const productCard = document.createElement('div');
            productCard.className = 'flex flex-col h-full product-card';
            productCard.innerHTML = `
            <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white p-2 border border-gray-300 transition-transform transform hover:scale-105 hover:shadow-xl hover:border-blue-500 cursor-pointer flex flex-col h-full ${outOfStock ? 'opacity-50 pointer-events-none' : ''}">
                <a href="/product/${product.product_id}">
                    ${product.main_image ? `<img class="w-full h-80 object-contain mb-4 rounded-lg" src="${product.main_image}" alt="Зображення товару">` : ''}
                    <p class="text-xl font-semibold text-gray-800 mb-2">${product.product_name}</p>
                    ${hasDiscount
                ? `<p class="text-lg font-medium text-gray-400 line-through mb-2">${product.product_price} грн</p>
                           <p class="text-xl font-semibold text-red-500 mb-2">${finalPrice} грн</p>`
                : `<p class="text-lg font-medium text-gray-800 mb-2">${product.product_price} грн</p>`
            }
                </a>
                <div class="flex-grow"></div>
                ${!outOfStock
                ? `<button class="add-to-cart-btn bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 hover:bg-blue-700"
                                data-name="${product.product_name}"
                                data-price="${finalPrice}"
                                data-image="${product.main_image}"
                                data-product-id="${product.product_id}">Купити</button>`
                : `<p class="text-red-500 text-center font-semibold mt-2">Немає в наявності</p>`
            }
            </div>
        `;
            fragment.appendChild(productCard);
        });

        productList.innerHTML = '';
        productList.appendChild(fragment);
    }

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        applyFilter();
    });

    resetForm.addEventListener('submit', function (e) {
        e.preventDefault();
        filterForm.reset();
        applyFilter();
    });

    sortForm.addEventListener('change', function (e) {
        e.preventDefault();
        applyFilter();
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        applyFilter();
    }
});
