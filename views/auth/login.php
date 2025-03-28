<?php
$title = 'Логін';
ob_start();
?>

<p id="error-message" style="color: red ;text-align: center; margin-bottom: 10px; display: none;"></p>

<form action="login.php" method="POST" id="loginForm" class="max-w-sm mx-auto bg-white p-6 rounded shadow-lg">
    <div class="mb-4">
        <label for="email" class="block text-gray-700">Email</label>
        <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваш email">
    </div>

    <div class="mb-4">
        <label for="password" class="block text-gray-700">Пароль</label>
        <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Введіть ваш пароль">
    </div>

    <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded mt-4">Увійти</button>
</form>

<p class="text-center mt-4">Ще не зареєстровані? <a href="/register" class="text-blue-500">Зареєструватися</a></p>

<?php
$content = ob_get_clean();
include '../views/layout.php';
?>
