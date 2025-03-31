<?php
$title = 'Профіль користувача';
ob_start();

$totalPrice = 0;
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">

    <?php if (!empty($cartItems)): ?>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200" id="cart">
        <div >
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
                <?php if (empty($cartItems)): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center">Ваш кошик порожній.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cartItems as $key => $item): ?>
                    <?php $totalPrice += $item['quantity'] * $item['price'] ?>
                        <tr id="cartItem-<?= $key ?>" class="hover:bg-gray-100">
                            <td class="px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" value="<?= $item['quantity'] ?>"
                                       class="quantity-input w-20 py-2 px-4 border rounded-lg" data-key="<?= $item['product_id'] ?>"/>
                            </td>
                            <td class="px-4 py-2"><?= htmlspecialchars($item['price']) ?> грн</td>
                            <td class="px-4 py-2">
                                <button class="remove-btn text-red-500 hover:underline" data-key="<?= $item['product_id'] ?>">
                                    Видалити
                                </button>
                            </td>
                            <td class="px-4 py-2 " style="display: none" ><?= htmlspecialchars($item['product_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
                </tbody>
            </table>
            <p id="totalPrice" class="text-right text-lg font-semibold ">Загальна сума: <?= $totalPrice ?> грн</p>

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
                            <input type="text" name="city" id="city" class="mt-2 px-4 py-2 border rounded-lg w-full"
                                   required>
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
    <?php endif; ?>

    <h1 class="text-3xl font-bold text-gray-900 mb-4 mt-5">Профіль</h1>

    <form action="/update-profile" method="POST" class="space-y-4" id="profileForm">
        <div>
            <label for="first_name" class="block text-gray-700">Ім'я:</label>
            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>"
                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <div>
            <label for="last_name" class="block text-gray-700">Прізвище:</label>
            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>"
                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>"
                   class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
            Оновити профіль
        </button>
    </form>

    <div class="mt-6">
        <h2 class="text-2xl font-semibold text-gray-900">Змінити пароль</h2>
        <form action="/change-password" method="POST" class="space-y-4" id="passwordForm">
            <div>
                <label for="new_password" class="block text-gray-700">Новий пароль:</label>
                <input type="password" name="new_password" id="new_password"
                       class="mt-2 px-4 py-2 border rounded-lg w-full" required>
            </div>
            <div>
                <label for="confirm_password" class="block text-gray-700">Підтвердження пароля:</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="mt-2 px-4 py-2 border rounded-lg w-full" required>
            </div>
            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Змінити пароль
            </button>
        </form>
    </div>

    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-900">Історія покупок</h2>
        <?php if (empty($orders)): ?>
            <p>У вас немає замовлень.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="border border-gray-300 p-4 rounded-md">
                        <p><strong>Дата:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                        <p><strong>Статус:</strong> <?= htmlspecialchars($order['status']) ?></p>
                        <p><strong>Сума:</strong> <?= htmlspecialchars($order['total_price']) ?> грн</p>

                        <h4 class="mt-4 text-md font-semibold">Товари:</h4>
                        <ul class="list-disc pl-5">
                            <?php foreach ($order['items'] as $item): ?>
                                <li><?= htmlspecialchars($item['product_name']) ?> - <?= htmlspecialchars($item['item_price']) ?> грн (Кількість: <?= htmlspecialchars($item['quantity']) ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>




</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
