<?php

require_once '../models/User.php';
session_start();

class AuthController {
    public function showRegisterForm($errorMessage = '') {
        $additionalScripts = '/scripts/auth/register.js';
        http_response_code(200);
        require '../views/auth/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['password'])) {
                echo json_encode(['success' => false, 'errorMessage' => "Усі поля обов'язкові для заповнення!", 'name' => $_POST['name'], 'email' => $_POST['email']]);
                http_response_code(400);
                return;
            }

            $userModel = new User();

            if ($userModel->emailExists($_POST['email'])) {
                echo json_encode(['success' => false, 'errorMessage' => "Користувач з таким email вже існує!", 'name' => $_POST['name'], 'email' => $_POST['email']]);
                http_response_code(400);
                return;
            }

            $userModel->register($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password']);

            $user = $userModel->login($_POST['email'], $_POST['password']);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                echo json_encode(['success' => true]);
                http_response_code(201);
                return;
            } else {
                echo json_encode(['success' => false, 'errorMessage' => 'Сталася помилка при авторизації після реєстрації']);
                http_response_code(500);
                return;
            }
        }
    }

    public function showLoginForm($errorMessage = '') {
        $additionalScripts = '/scripts/auth/login.js';
        require '../views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['email']) || empty($_POST['password'])) {
                echo json_encode(['success' => false, 'errorMessage' => "Будь ласка, введіть email і пароль!", 'email' => $_POST['email'], 'password' => $_POST['password']]);
                http_response_code(400);
                return;
            }

            $userModel = new User();
            $user = $userModel->login($_POST['email'], $_POST['password']);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                echo json_encode(['success' => true]);
                http_response_code(200);
                return;
            } else {
                echo json_encode(['success' => false, 'errorMessage' => 'Невірний email або пароль', 'email' => $_POST['email'], 'password' => $_POST['password']]);
                http_response_code(401);
                return;
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}
