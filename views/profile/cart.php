<?php
$title = 'Профіль користувача';
ob_start();

$totalPrice = 0;
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200" >

    <div class="text-center text-gray-700 text-xl font-semibold p-6 hidden" id="emptyCart">
        Ваш кошик порожній.
    </div>
    <div class="text-center text-gray-700 text-xl font-semibold p-6 hidden" id="successCart">
        Замовлення оформлено успішно.
    </div>

    <?php if (!empty($cartItems)): ?>
        <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200" id="cart">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Кошик</h2>
                <table class="min-w-full mt-4" id="cartTable">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Товар</th>
                        <th class="px-4 py-2 text-left font-semibold">Кількість</th>
                        <th class="px-4 py-2 text-left font-semibold">Ціна</th>
                        <th class="px-4 py-2 text-left font-semibold">Дія</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartItems as $key => $item): ?>
                        <?php $totalPrice += $item['quantity'] * $item['price'] ?>
                        <tr id="cartItem-<?= $key ?>" class="hover:bg-gray-100">
                            <td class="px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" value="<?= $item['quantity'] ?>"
                                       class="quantity-input w-20 py-2 px-4 border rounded-lg"
                                       data-key="<?= $key ?>"/>
                            </td>
                            <td class="px-4 py-2"><?= htmlspecialchars($item['price']) ?> грн</td>
                            <td class="px-4 py-2">
                                <button class="remove-btn bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                                        data-key="<?= $key ?>">
                                    Видалити
                                </button>
                            </td>
                            <td class="px-4 py-2" style="display: none" id="product_id"><?= htmlspecialchars($item['product_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>

                <p id="totalPrice" class="text-right text-lg font-semibold">Загальна сума: <?= $totalPrice ?> грн</p>

                <div class="mt-4">
                    <h2 class="text-2xl font-semibold text-gray-900">Оформити замовлення</h2>
                    <form id="orderForm" class="space-y-4">
                        <div>
                            <label for="address" class="block text-gray-700">Адреса доставки:</label>
                            <input type="text" name="address" id="address"
                                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
                        </div>
                        <div>
                            <label for="city" class="block text-gray-700">Місто:</label>
                            <input type="text" name="city" id="city"
                                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
                        </div>
                        <div>
                            <label for="postal_code" class="block text-gray-700">Поштовий код:</label>
                            <input type="text" name="postal_code" id="postal_code"
                                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
                        </div>
                        <div class="flex justify-between">
                            <button type="submit"
                                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                Оформити замовлення
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center text-gray-700 text-xl font-semibold p-6">
            Ваш кошик порожній.
        </div>
    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
