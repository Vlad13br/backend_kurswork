<?php
require_once '../core/Database.php';
require_once '../models/User.php';
require_once '../models/Order.php';

class ProfileController
{
    public function showProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getUserById($_SESSION['user_id']);

        $orderModel = new Order();
        $orders = $orderModel->getUserOrders($_SESSION['user_id']);

        $cartItems = $_SESSION['cart'] ?? [];

        $additionalScripts = '/scripts/profile/profile.js';
        require '../views/profile/profile.php';
    }

    public function updateProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['message' => 'Не авторизовано']);
            exit;
        }

        $userModel = new User();
        $userModel->updateUser(
            $_SESSION['user_id'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email']
        );

        echo json_encode(['message' => 'Профіль оновлено успішно.']);
        exit;
    }

    public function changePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['message' => 'Не авторизовано']);
            exit;
        }

        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $userModel = new User();
            $userModel->changePassword(
                $_SESSION['user_id'],
                $_POST['new_password']
            );

            echo json_encode(['message' => 'Пароль змінено успішно.']);
        } else {
            echo json_encode(['message' => 'Паролі не співпадають']);
        }
        exit;
    }
}
?>
