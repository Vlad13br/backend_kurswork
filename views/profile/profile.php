<?php
$title = 'Профіль користувача';
ob_start();
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">
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
                <tr id="cartItem-<?= $key ?>" class="hover:bg-gray-100">
                    <td class="px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
                    <td class="px-4 py-2">
                        <input type="number" min="1" value="<?= $item['quantity'] ?>" class="quantity-input w-16 py-2 px-4 border rounded-lg" data-key="<?= $key ?>" />
                    </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($item['price']) ?> грн</td>
                    <td class="px-4 py-2">
                        <button class="remove-btn text-red-500 hover:underline" data-key="<?= $key ?>">Видалити</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <h1 class="text-3xl font-bold text-gray-900 mb-4 mt-5">Профіль</h1>

    <form action="/update-profile" method="POST" class="space-y-4" id="profileForm">
        <div>
            <label for="first_name" class="block text-gray-700">Ім'я:</label>
            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <div>
            <label for="last_name" class="block text-gray-700">Призвище:</label>
            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <div>
            <label for="email" class="block text-gray-700">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
            Оновити профіль
        </button>
    </form>

    <div class="mt-6">
        <h2 class="text-2xl font-semibold text-gray-900">Змінити пароль</h2>
        <form action="/change-password" method="POST" class="space-y-4" id="passwordForm">
            <div>
                <label for="new_password" class="block text-gray-700">Новий пароль:</label>
                <input type="password" name="new_password" id="new_password" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
            </div>
            <div>
                <label for="confirm_password" class="block text-gray-700">Підтвердження пароля:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Змінити пароль
            </button>
        </form>
    </div>
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-900">Історія покупок</h2>
        <?php if (empty($orders)): ?>
            <p>У вас немає замовлень.</p>
        <?php else: ?>
            <table class="min-w-full mt-4">
                <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Номер замовлення</th>
                    <th class="px-4 py-2 text-left">Дата</th>
                    <th class="px-4 py-2 text-left">Статус</th>
                    <th class="px-4 py-2 text-left">Сума</th>
                    <th class="px-4 py-2 text-left">Дія</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="px-4 py-2"><?= htmlspecialchars($order['id']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($order['created_at']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($order['status']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($order['total_price']) ?> грн</td>
                        <td class="px-4 py-2">
                            <a href="/order/<?= htmlspecialchars($order['id']) ?>" class="text-blue-500 hover:underline">Переглянути</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
