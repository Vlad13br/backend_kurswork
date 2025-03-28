<?php
$title = 'Додати товар';
ob_start();
?>

<h2 class="text-2xl font-bold mb-4">Додати товар</h2>
<form action="/store-product" method="post" enctype="multipart/form-data" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">Категорія:
            <select name="category_id" onchange="fetchAttributes(this.value)" required
                    class="border p-2 w-full rounded">
                <option value="">Оберіть категорію</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="block brand-field"">Бренд:
        <select name="brand_id" class="border p-2 w-full rounded" required>
            <option value="">Оберіть бренд</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
            <?php endforeach; ?>
        </select>
        </label>

        <label class="block">Назва товару:
            <input type="text" name="name" required class="border p-2 w-full rounded">
        </label>

        <label class="block">Опис:
            <textarea name="description" required class="border p-2 w-full rounded"></textarea>
        </label>

        <label class="block">Ціна:
            <input type="number" name="price" step="0.01" required class="border p-2 w-full rounded">
        </label>

        <label class="block">Кількість:
            <input type="number" name="stock" required class="border p-2 w-full rounded">
        </label>


        <label class="block">Знижка (%):
            <input type="number" name="discount" step="0.01" class="border p-2 w-full rounded">
        </label>

        <label class="block">Додати фото:
            <input type="file" name="images[]" multiple class="border p-2 w-full rounded" onchange="previewImages(event)">
        </label>

        <div id="image-preview" class="grid grid-cols-3 gap-2"></div>
        <input type="hidden" name="main_image" id="main_image">

    </div>


    <div id="attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4"></div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full mt-4">Додати товар</button>
</form>
</div>
<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
