document.addEventListener("DOMContentLoaded", () => {
    const profileForm = document.querySelector("#profileForm");
    const passwordForm = document.querySelector("#passwordForm");

    if (profileForm) {
        profileForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(profileForm);
            try {
                const response = await fetch('/update-profile', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(result.message || "Профіль оновлено успішно.");
                } else {
                    const error = await response.json();
                    alert(error.message || "Сталася помилка при оновленні профілю.");
                }
            } catch (error) {
                alert("Помилка з'єднання з сервером.");
            }
        });
    }

    if (passwordForm) {
        passwordForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(passwordForm);
            try {
                const response = await fetch('/change-password', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(result.message || "Пароль змінено успішно.");
                } else {
                    const error = await response.json();
                    alert(error.message || "Сталася помилка при зміні пароля.");
                }
            } catch (error) {
                alert("Помилка з'єднання з сервером.");
            }
        });
    }

});


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const productId = this.dataset.key;
            const quantity = this.value;

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
        });
    });

    document.querySelectorAll('.remove-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.dataset.key;

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
                    } else {
                        console.error('Немає оновленого кошика');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});

function updateCartTable(cart) {
    const cartTableBody = document.querySelector('#cartTable tbody');
    cartTableBody.innerHTML = '';

    cart.forEach(function(item, index) {
        const row = document.createElement('tr');
        row.id = 'cartItem-' + index;
        row.classList.add('hover:bg-gray-100');

        row.innerHTML = `
            <td class="px-4 py-2">${item.name}</td>
            <td class="px-4 py-2">
                <input type="number" min="1" value="${item.quantity}" class="quantity-input w-16 py-2 px-4 border rounded-lg" data-key="${index}" />
            </td>
            <td class="px-4 py-2">${item.price} грн</td>
            <td class="px-4 py-2">
                <button class="remove-btn text-red-500 hover:underline" data-key="${index}">Видалити</button>
            </td>
        `;

        cartTableBody.appendChild(row);
    });

    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const productId = this.dataset.key;
            const quantity = this.value;

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
        });
    });

    document.querySelectorAll('.remove-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.dataset.key;

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
        });
    });
}
