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
</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
