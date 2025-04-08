<?php
$title = 'Профіль користувача';
ob_start();

$totalPrice = 0;
?>

    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">

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