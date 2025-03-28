<?php

$title = 'Головна';
ob_start();
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
        <a href="/product/<?= $product['product_id'] ?>" class="flex flex-col">
            <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white p-2 border border-gray-300 transition-transform transform hover:scale-105 hover:shadow-xl hover:border-blue-500 cursor-pointer flex flex-col h-full">
                <?php if (!empty($product['main_image'])): ?>
                    <img class="w-full h-80 object-contain mb-4 rounded-lg" src="<?= htmlspecialchars($product['main_image']) ?>" alt="Зображення товару">
                <?php endif; ?>
                <div class="p-4 flex-grow">
                    <p class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($product['product_name']) ?></p>

                    <?php if (!empty($product['product_discount']) && $product['product_discount'] > 0): ?>
                        <p class="text-lg font-medium text-gray-400 line-through mb-2"><?= htmlspecialchars($product['product_price']) ?> грн</p>
                        <p class="text-xl font-semibold text-red-500 mb-2">
                            <?= number_format($product['product_price'] * (1 - $product['product_discount'] / 100), 2) ?> грн
                        </p>
                    <?php else: ?>
                        <p class="text-lg font-medium text-gray-800 mb-2"><?= htmlspecialchars($product['product_price']) ?> грн</p>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
