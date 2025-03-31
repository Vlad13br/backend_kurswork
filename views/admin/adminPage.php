<?php
$title = 'Адмінка';
ob_start();
?>

<div class="max-w-7xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">

    <table class="min-w-full table-auto">
        <thead>
        <tr class="bg-gray-100 text-gray-600">
            <th class="px-2 py-2 text-left">№ Замовлення</th>
            <th class="px-4 py-2 text-left">Статус</th>
            <th class="px-4 py-2 text-left">Адреса</th>
            <th class="px-4 py-2 text-left">Дата створення</th>
            <th class="px-4 py-2 text-left">Користувач</th>
            <th class="px-4 py-2 text-left w-64">Товари</th>
            <th class="px-4 py-2 text-left">Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $groupedOrders = [];
        foreach ($orders as $item) {
            $groupedOrders[$item['order_id']][] = $item;
        }

        foreach ($groupedOrders as $orderId => $items):
            $order = $items[0];
            ?>
            <tr class="border-t" id="order-row-<?php echo $order['order_id']; ?>">
                <td class="px-4 py-2"><?php echo $order['order_id']; ?></td>
                <td class="px-4 py-2">
                    <select id="status-<?php echo $order['order_id']; ?>" onchange="updateOrderStatus(<?php echo $order['order_id']; ?>)">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Очікується</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Обробляється</option>
                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Відправлено</option>
                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Доставлено</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Скасовано</option>
                    </select>
                </td>
                <td class="px-4 py-2 w-56"><?php echo $order['address']; ?>, <?php echo $order['city']; ?>, <?php echo $order['postal_code']; ?></td>
                <td class="px-4 py-2 w-56"><?php echo $order['created_at']; ?></td>
                <td class="px-4 py-2 w-64" >
                    <p><strong>Ім'я:</strong> <?php echo $order['user_first_name']; ?> <?php echo $order['user_last_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['user_email']; ?></p>
                </td>
                <td class="px-4 py-2 w-64">
                    <ul>
                        <?php
                        foreach ($items as $item) { ?>
                            <li class="border-b border-gray-300 p-2 mb-2">
                                <p class="font-semibold">Продукт: <?php echo $item['product_name']; ?></p>
                                <p>Кількість: <?php echo $item['quantity']; ?></p>
                                <p>Ціна: <?php echo $item['price']; ?> грн</p>
                            </li>
                        <?php }
                        ?>
                    </ul>
                </td>
                <td class="px-4 py-2">
                    <button onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'shipped')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 mb-3">Редагувати</button>
                    <button onclick="deleteOrder(<?php echo $order['order_id']; ?>)" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Видалити</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>