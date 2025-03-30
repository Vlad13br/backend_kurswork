<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Сторінка' ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-900">

<header class="bg-gray-600 text-white p-4 shadow-md fixed w-full top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="/" class="text-2xl ">Головна</a>
        <div class="flex space-x-4">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/admin" class="hover:text-blue-200">Адмінка</a>
                <a href="/add-category" class="hover:text-blue-200">Додати категорію</a>
                <a href='/add-brand' class="hover:text-blue-200">Додати бренд</a>
                <a href="/add-product" class="hover:text-blue-200">Додати товар</a>
                <a href="/logout" class="hover:text-blue-200">Вийти</a>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <a href="/profile" class="hover:text-blue-200">Профіль</a>
                <a href="/logout" class="hover:text-blue-200">Вийти</a>
            <?php else: ?>
                <a href="/login" class="hover:text-blue-200">Увійти</a>
                <a href="/register" class="hover:text-blue-200">Реєстрація</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="container mx-auto mt-24 p-6">
    <?= $content ?? '' ?>
</div>

<?php if (isset($additionalScripts)): ?>
    <script src="<?= $additionalScripts; ?>"></script>
<?php endif; ?>

<?php if (isset($additionalScripts1)): ?>
    <script src="<?= $additionalScripts1; ?>"></script>
<?php endif; ?>


</body>
</html>
