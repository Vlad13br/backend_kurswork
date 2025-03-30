<?php
$title = 'Детальна інформація про товар';
ob_start();
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <?php if (!empty($product['images'])): ?>
                <?php
                $product['images'] = array_unique($product['images']);
                ?>
                <img id="mainImage" class="w-full h-96 object-contain rounded-lg mt-4 mb-4"
                     src="<?= htmlspecialchars($product['images'][0]) ?>" alt="Зображення товару">
                <div class="flex justify-center space-x-2">
                    <?php foreach ($product['images'] as $image): ?>
                        <img class="w-24 h-24 object-cover rounded-lg border cursor-pointer"
                             src="<?= htmlspecialchars($image) ?>"
                             alt="Зображення товару"
                             onclick="changeImage('<?= htmlspecialchars($image) ?>')">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="w-full h-96 flex items-center justify-center bg-gray-200 rounded-lg">
                    <p class="text-gray-500">Немає зображення</p>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <form action="/edit-product/<?= $product['id'] ?>" method="GET">
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Редагувати
                    </button>
                </form>
            <?php endif; ?>

            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-lg text-gray-700 mb-2"><strong>Бренд:</strong> <?= htmlspecialchars($product['brand']) ?></p>
            <p class="text-lg text-gray-700 mb-2 break-words">
                <strong>Опис:</strong> <?= htmlspecialchars($product['description']) ?></p>

            <?php if (!empty($product['discount']) && $product['discount'] > 0): ?>
                <p class="text-lg font-medium text-gray-400 line-through mb-2"><?= htmlspecialchars($product['product_price']) ?>
                    грн</p>
                <p class="text-xl font-semibold text-red-500 mb-2">
                    <?= number_format($product['product_price'] * (1 - $product['discount'] / 100), 2) ?> грн
                </p>
            <?php else: ?>
                <p class="text-lg font-medium text-gray-800 mb-2"><?= htmlspecialchars($product['product_price']) ?>
                    грн</p>
            <?php endif; ?>
            <p class="text-lg text-gray-700 mt-4">
                <strong>Наявність:</strong> <?= $product['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності' ?></p>
            <button class="add-to-cart-btn bg-blue-500 text-white px-4 py-2 rounded-lg mt-2 hover:bg-blue-700"
                    data-name="<?= htmlspecialchars($product['name']) ?>"
                    data-price="<?= number_format($product['product_price'] * (1 - $product['discount'] / 100), 2) ?>"
                    data-image="<?= htmlspecialchars($product['images'][0]) ?>"
                    data-product-id="<?= $product['id'] ?>">Купити
            </button>
        </div>
    </div>

    <?php if (!empty($product['attributes'])): ?>
        <div class="mt-6">
            <h2 class="text-2xl font-semibold text-gray-900">Характеристики</h2>
            <ul class="mt-2 border border-gray-200 rounded-lg p-4 bg-gray-50">
                <?php foreach ($product['attributes'] as $attribute): ?>
                    <li class="text-gray-700 mb-1">
                        <strong><?= htmlspecialchars($attribute['attribute_name']) ?>
                            :</strong> <?= htmlspecialchars($attribute['attribute_value']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mt-6">
            <h2 class="text-2xl font-semibold text-gray-900">Залиште коментар</h2>
            <form id="commentForm" class="space-y-4" action="/add-comment" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>" id="productId">
                <div>
                    <label for="rating" class="block text-gray-700">Рейтинг:</label>
                    <select name="rating" id="rating" class="mt-2 px-4 py-2 border rounded-lg w-full" required>
                        <option value="1">1 - Дуже погано</option>
                        <option value="2">2 - Погано</option>
                        <option value="3">3 - Середньо</option>
                        <option value="4">4 - Добре</option>
                        <option value="5">5 - Чудово</option>
                    </select>
                </div>
                <div>
                    <label for="comment" class="block text-gray-700">Коментар:</label>
                    <textarea name="comment" id="comment" rows="4" class="mt-2 px-4 py-2 border rounded-lg w-full"
                              required></textarea>
                </div>
                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Залишити коментар
                </button>
            </form>

        </div>

    <?php else: ?>
        <p class="text-gray-500 mt-4">Вам потрібно увійти, щоб залишити коментар.</p>
    <?php endif; ?>

    <div class="mt-6">
        <h2 class="text-2xl font-semibold text-gray-900">Коментарі</h2>
        <?php if (!empty($comments)): ?>
            <ul class="mt-4 space-y-4" id="commentList">
                <?php foreach ($comments as $comment): ?>
                    <li class="border-b border-gray-200 pb-4">
                        <p class="font-semibold"><?= htmlspecialchars($comment['first_name']) ?> </p>
                        <p class="text-yellow-500">Рейтинг: <?= $comment['rating'] ?>/5</p>
                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">Немає коментарів до цього товару.</p>
        <?php endif; ?>
    </div>
</div>

<div id="cart-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-xl w-2/3 max-w-xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Кошик</h2>
        <div id="cart-items" class="space-y-4"></div>
        <div class="flex justify-between mt-6 gap-4">
            <button onclick="closeCart()"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                Продовжити покупки
            </button>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/profile" class=" bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">Оформити замовлення</a>
            <?php else: ?>
                <a href="/profile" class=" bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">Увійти для оформлення замовлення</a>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
