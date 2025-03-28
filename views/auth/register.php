<?php
$title = 'Реєстрація';
ob_start();
?>

<p id="error-message" style="color: red ;text-align: center; margin-bottom: 10px; display: none;"></p>

<form action="register.php" method="POST" id="registerForm" class="max-w-sm mx-auto bg-white p-6 rounded shadow-lg">
    <div class="mb-4">
        <label for="first_name" class="block text-gray-700">Ім'я</label>
        <input type="text" id="first_name" name="first_name" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваше ім'я">
    </div>

    <div class="mb-4">
        <label for="last_name" class="block text-gray-700">Прізвище</label>
        <input type="text" id="last_name" name="last_name" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваше прізвище">
    </div>

    <div class="mb-4">
        <label for="email" class="block text-gray-700">Email</label>
        <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваш email">
    </div>

    <div class="mb-4">
        <label for="password" class="block text-gray-700">Пароль</label>
        <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваш пароль">
    </div>

    <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded mt-4">Зареєструватися</button>
</form>

<p class="text-center mt-4">Вже маєте аккаунт? <a href="/login" class="text-blue-500">Увійти</a></p>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
