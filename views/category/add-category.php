<?php
$title = 'Створити категорію';
ob_start();
?>

<div class="flex max-w-4xl mx-auto">
    <div class="w-1/3 bg-gray-100 p-4 rounded shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Список категорій</h2>
        <ul class="space-y-4">
            <?php foreach ($categories as $category): ?>
                <li class="p-2 bg-white rounded shadow">
                    <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                    <?php if (!empty($category['attributes'])): ?>
                        <ul class="mt-2 ml-4 text-sm text-gray-600">
                            <?php foreach ($category['attributes'] as $attribute): ?>
                                <li>- <?php echo htmlspecialchars($attribute); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">Атрибути відсутні</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="w-2/3 bg-white p-6 ml-4 rounded shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Створити нову категорію</h2>
        <p id="error-message" style="color: red; display: none;"></p>

        <form action="/add-category/store" method="POST" id="createCategoryForm">
            <div class="mb-4">
                <label for="category_name" class="block text-gray-700">Назва категорії</label>
                <input type="text" id="category_name" name="category_name" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть назву категорії">
            </div>

            <div id="attributes-container" class="mb-4">
                <label class="block text-gray-700">Атрибути категорії</label>
                <div class="attribute-row mb-2">
                    <input type="text" name="attribute_name[]" class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Назва атрибуту">
                </div>
            </div>

            <button type="button" id="add-attribute" class="w-full p-2 bg-gray-300 text-black rounded mt-4">Додати атрибут</button>
            <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded mt-4">Створити категорію</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
