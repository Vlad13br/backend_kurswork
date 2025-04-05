<?php

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$title = 'Головна';
ob_start();
?>

<div class="flex justify-end mb-4">
    <label for="sort" class="mr-2 text-gray-700">Сортувати за:</label>
    <select id="sort" class="p-2 border rounded" onchange="applySort()">
        <?php $sort = $_GET['sort'] ?? ''; ?>
        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Новизною</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Ціна: за зростанням</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Ціна: за спаданням</option>
    </select>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-1 bg-white p-4 shadow-lg rounded-lg border border-gray-300">
        <h2 class="text-xl font-semibold mb-4">Фільтри</h2>
        <form id="filter-form" method="GET" onsubmit="applyFilter(event)">
            <label class="block mb-2">
                <span class="text-gray-700">Мінімальна ціна:</span>
                <input type="number" name="min_price" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>"
                       class="w-full p-2 border rounded-lg" placeholder="Від">
            </label>
            <label class="block mb-2">
                <span class="text-gray-700">Максимальна ціна:</span>
                <input type="number" name="max_price" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>"
                       class="w-full p-2 border rounded-lg" placeholder="До">
            </label>
            <label class="block mb-2">
                <span class="text-gray-700">Оберіть категорію:</span>
                <select name="category" class="w-full p-2 border rounded-lg">
                    <option value="">Всі категорії</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '' ?>>
                            <?= $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-700">Застосувати
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:col-span-3" id="product-list">
        <?php if (empty($products)): ?>
            <p class="text-center text-gray-600">Товарів не знайдено за заданими критеріями.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php $outOfStock = $product['product_stock'] <= 0; ?>
                <div class="flex flex-col h-full">
                    <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white p-2 border border-gray-300 transition-transform transform hover:scale-105 hover:shadow-xl hover:border-blue-500 cursor-pointer flex flex-col h-full <?= $outOfStock ? 'opacity-50 pointer-events-none' : '' ?>">
                        <a href="/product/<?= $product['product_id'] ?>">
                            <?php if (!empty($product['main_image'])): ?>
                                <img class="w-full h-80 object-contain mb-4 rounded-lg"
                                     src="<?= htmlspecialchars($product['main_image']) ?>" alt="Зображення товару">
                            <?php endif; ?>
                            <p class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($product['product_name']) ?></p>
                            <?php
                            $final_price = $product['product_price'];
                            if (!empty($product['product_discount']) && $product['product_discount'] > 0) {
                                $final_price = $product['product_price'] * (1 - $product['product_discount'] / 100);
                                echo "<p class='text-lg font-medium text-gray-400 line-through mb-2'>" . htmlspecialchars($product['product_price']) . " грн</p>";
                                echo "<p class='text-xl font-semibold text-red-500 mb-2'>" . number_format($final_price, 2) . " грн</p>";
                            } else {
                                echo "<p class='text-lg font-medium text-gray-800 mb-2'>" . htmlspecialchars($product['product_price']) . " грн</p>";
                            }
                            ?>
                        </a>
                        <div class="flex-grow"></div>
                        <?php if (!$outOfStock): ?>
                            <button class="add-to-cart-btn bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 hover:bg-blue-700"
                                    data-name="<?= htmlspecialchars($product['product_name']) ?>"
                                    data-price="<?= $final_price ?>"
                                    data-image="<?= htmlspecialchars($product['main_image']) ?>"
                                    data-product-id="<?= $product['product_id'] ?>">Купити
                            </button>
                        <?php else: ?>
                            <p class="text-red-500 text-center font-semibold mt-2">Немає в наявності</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>


    </div>
</div>

<div class="mt-5">
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&sort=<?= urlencode($sort) ?>"
               class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                <span class="sr-only">Previous</span>
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
        <?php else: ?>
            <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset">
                <span class="sr-only">Previous</span>
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                          clip-rule="evenodd"/>
                </svg>
            </span>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= urlencode($sort) ?>"
               class="relative inline-flex items-center px-4 py-2 text-sm font-semibold <?= $i == $page ? 'bg-indigo-600 text-white' : 'text-gray-900' ?> ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&sort=<?= urlencode($sort) ?>"
               class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset focus:z-20 focus:outline-offset-0">
                <span class="sr-only">Next</span>
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
        <?php else: ?>
            <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-gray-300 ring-inset">
                <span class="sr-only">Next</span>
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                          clip-rule="evenodd"/>
                </svg>
            </span>
        <?php endif; ?>
    </nav>
</div>

<div id="cart-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-xl w-2/3 max-w-xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Кошик</h2>
        <div id="cart-items" class="space-y-4"></div>
        <p id="price">Total price</p>
        <div class="flex justify-between mt-6 gap-4">
            <button onclick="closeCart()"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                Продовжити покупки
            </button>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/profile"
                   class=" bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">Оформити
                    замовлення</a>
            <?php else: ?>
                <a href="/profile"
                   class=" bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">Увійти
                    для оформлення замовлення</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout.php';
?>
