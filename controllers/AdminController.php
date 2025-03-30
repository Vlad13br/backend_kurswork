<?php
require_once '../core/Database.php';
require_once '../models/Order.php';

class AdminController
{
    public function showProfile()
    {

        $orderModel = new Order();
        $orders = $orderModel->getAllOrders();

        $cartItems = $_SESSION['cart'] ?? [];

        $additionalScripts = '/scripts/admin/admin.js';
        require '../views/admin/adminPage.php';
    }


    public function updateOrderStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['message' => 'Недостатньо прав']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['order_id']) && isset($data['status'])) {
            $orderModel = new Order();
            $orderId = $data['order_id'];
            $status = $data['status'];

            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (in_array($status, $validStatuses)) {
                $orderModel->updateOrderStatus($orderId, $status);
                echo json_encode(['message' => 'Статус замовлення оновлено']);
            } else {
                echo json_encode(['message' => 'Невірний статус']);
            }
        } else {
            echo json_encode(['message' => 'Недостатньо даних']);
        }

        exit;
    }

    public function deleteOrder()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['message' => 'Недостатньо прав']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['order_id'])) {
            $orderModel = new Order();
            $orderId = $data['order_id'];

            $orderModel->deleteOrder($orderId);
            echo json_encode(['message' => 'Замовлення видалено']);
        } else {
            echo json_encode(['message' => 'Недостатньо даних']);
        }

        exit;
    }

}
?>
