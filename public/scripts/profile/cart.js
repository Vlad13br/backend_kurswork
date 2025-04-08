document.addEventListener('DOMContentLoaded', function () {
    const cartTable = document.getElementById('cartTable');

    cartTable.addEventListener('change', function (e) {
        if (e.target.classList.contains('quantity-input')) {
            const productId = e.target.dataset.key;
            const quantity = e.target.value;

            if (quantity < 1) {
                alert('Кількість товару повинна бути більше або рівно 1');
                return;
            }

            fetch('/update-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.cart) {
                        updateCartTable(data.cart);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    cartTable.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-btn')) {
            const productId = e.target.dataset.key;

            fetch('/remove-from-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.cart) {
                        updateCartTable(data.cart);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    document.getElementById('orderForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const address = document.getElementById('address').value.trim();
        const city = document.getElementById('city').value.trim();
        const postalCode = document.getElementById('postal_code').value.trim();

        if (!address || !city || !postalCode) {
            alert('Будь ласка, заповніть усі поля.');
            return;
        }

        const products = [];
        document.querySelectorAll('.quantity-input').forEach(input => {
            const productId = parseInt(document.getElementById('product_id').textContent, 10);
            const quantity = parseInt(input.value, 10);
            if (quantity > 0 && productId) {
                products.push({product_id: productId, quantity});
            }
        });

        if (products.length==0) {
            alert('Додайте товари в кошик')
            return
        }

        try {
            const response = await fetch('/place-order', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({address, city, postal_code: postalCode, products})
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Помилка оформлення замовлення');

            alert(data.message);

            document.getElementById('orderForm').style.display = 'none';
            document.getElementById('cart').style.display = 'none';
            document.getElementById('orderForm').reset();
            document.getElementById('successCart').classList.remove('hidden');

        } catch (error) {
            console.error('Помилка:', error);
            alert(error.message);
        }
    });

});

function updateCartTable(cart) {
    const cartContainer = document.getElementById('cart');
    const cartTableBody = document.querySelector('#cartTable tbody');
    const totalPriceElement = document.querySelector('#totalPrice');

    if (cart.length === 0) {
        cartContainer.style.display = 'none';
        document.getElementById('emptyCart').classList.remove('hidden');
        return;
    } else {
        cartContainer.style.display = 'block';
        document.getElementById('emptyCart').classList.add('hidden');
    }

    cartTableBody.innerHTML = '';
    let totalPrice = 0;

    cart.forEach(function (item, index) {
        const row = document.createElement('tr');
        row.id = 'cartItem-' + index;
        row.classList.add('hover:bg-gray-100');
        row.innerHTML = `
            <td class="px-4 py-2">${item.name}</td>
            <td class="px-4 py-2">
                <input type="number" min="1" value="${item.quantity}" class="quantity-input w-20 py-2 px-4 border rounded-lg" data-key="${index}" />
            </td>
            <td class="px-4 py-2">${item.price} грн</td>
            <td class="px-4 py-2">
                <button class="remove-btn bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200" data-key="${index}">Видалити</button>
            </td>
             <td class="px-4 py-2" style="display: none" id="product_id">${item.product_id}</td>
      `;

        cartTableBody.appendChild(row);

        totalPrice += item.price * item.quantity;
    });

    totalPriceElement.textContent = `Загальна сума: ${totalPrice.toFixed(2)} грн`;
}
