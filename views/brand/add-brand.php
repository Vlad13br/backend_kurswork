<?php
$title = 'Створити бренд';
ob_start();
?>

<div class="flex max-w-4xl mx-auto">
    <div class="w-1/3 bg-gray-100 p-4 rounded shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Список брендів</h2>
        <ul class="space-y-2">
            <?php foreach ($brands as $brand): ?>
                <li class="p-2 bg-white rounded shadow"><?php echo htmlspecialchars($brand['name']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="w-2/3 bg-white p-6 ml-4 rounded shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Створити новий бренд</h2>
        <p id="error-message" style="color: red; display: none;"></p>

        <form action="/add-brand/store" method="POST" id="createBrandForm">
            <div class="mb-4">
                <label for="brand_name" class="block text-gray-700">Назва бренду</label>
                <input type="text" id="brand_name" name="brand_name" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть назву бренду">
            </div>

            <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded mt-4">Створити бренд</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
