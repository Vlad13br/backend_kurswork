<?php
$title = 'Детальна інформація про товар';
ob_start();
?>

<form method="POST" action="/edit-product/<?php echo $product['id']; ?>" enctype="multipart/form-data" class="max-w-2xl mx-auto p-6 border rounded-lg shadow-lg bg-white">
    <div class="mb-4">
        <label for="name" class="block text-gray-700 font-semibold mb-2">Назва товару</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <div class="mb-4">
        <label for="description" class="block text-gray-700 font-semibold mb-2">Опис товару</label>
        <textarea name="description" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"><?php echo $product['description']; ?></textarea>
    </div>

    <div class="mb-4">
        <label for="price" class="block text-gray-700 font-semibold mb-2">Ціна</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <div class="mb-4">
        <label for="stock" class="block text-gray-700 font-semibold mb-2">Кількість на складі</label>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <div class="mb-4">
        <label for="discount" class="block text-gray-700 font-semibold mb-2">Знижка (%)</label>
        <input type="number" name="discount" value="<?php echo $product['discount']; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <div class="mb-4">
        <button type="submit" class="w-full p-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Оновити товар</button>
    </div>
</form>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
