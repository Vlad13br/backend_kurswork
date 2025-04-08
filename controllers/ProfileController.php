<?php
require_once '../core/Database.php';
require_once '../models/User.php';
require_once '../models/Order.php';

class ProfileController
{
    public function showProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getUserById($_SESSION['user_id']);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'Користувача не знайдено']);
            exit;
        }

        $orderModel = new Order();
        $orders = $orderModel->getUserOrders($_SESSION['user_id']);

        $cartItems = $_SESSION['cart'] ?? [];

        $additionalScripts = '/scripts/profile/profile.js';
        require '../views/profile/profile.php';
    }
    public function updateProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Не авторизовано']);
            exit;
        }

        if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Всі поля повинні бути заповнені']);
            exit;
        }

        $userModel = new User();
        $isUpdated = $userModel->updateUser(
            $_SESSION['user_id'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email']
        );

        if ($isUpdated) {
            http_response_code(200);
            echo json_encode(['message' => 'Профіль оновлено успішно.']);
        } else {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }
        exit;
    }
    public function showCart()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Location: /login');
            exit;
        }

        $cartItems = $_SESSION['cart'] ?? [];

        $additionalScripts = '/scripts/profile/cart.js';
        require '../views/profile/cart.php';
    }

    public function changePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Не авторизовано']);
            exit;
        }

        if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Всі поля повинні бути заповнені']);
            exit;
        }

        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $userModel = new User();
            $isChanged = $userModel->changePassword(
                $_SESSION['user_id'],
                $_POST['new_password']
            );

            if ($isChanged) {
                http_response_code(200);
                echo json_encode(['message' => 'Пароль змінено успішно.']);
            } else {
                http_response_code(500);
                require '../views/500.php';
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Паролі не співпадають']);
        }
        exit;
    }
}
?>
