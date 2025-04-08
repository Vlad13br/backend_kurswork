document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price.replace(',', '.'));
            const image = this.dataset.image;
            const productId = this.dataset.productId;

            addToCart(name, price, image, productId);
        });
    });

    document.querySelectorAll('.show-cart-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            showCart();
        });
    });
});

function addToCart(name, price, image, productId) {
    fetch('/add-to-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({name, price, image, product_id: productId})
    })
        .then(response => response.json())
        .then(data => {
            showCart();
        })
        .catch(error => console.error('Error:', error));
}

function showCart() {
    fetch('/get-cart')
        .then(response => response.json())
        .then(data => {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            data.cart.forEach(item => {
                cartItems.innerHTML += `<div class='flex items-center justify-between border-b py-2'>
                    <img src="${item.image}" class="w-16 h-16 object-cover rounded mr-2">
                    <p>${item.name} (${item.quantity} шт.)</p>
                    <p>${(item.price * item.quantity).toFixed(2)} грн</p>
                </div>`;
            });
            document.getElementById('price').innerText = `Загальна сума замовлення: ${data.totalPrice.toFixed(2)} грн`;

            document.getElementById('cart-modal').classList.remove('hidden');
        });
}

function closeCart() {
    document.getElementById('cart-modal').classList.add('hidden');
}