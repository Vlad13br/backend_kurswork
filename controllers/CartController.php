<?php

class CartController
{
    public function showCart()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $totalPrice = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        echo json_encode(['cart' => $_SESSION['cart'], 'totalPrice' => $totalPrice]);
        http_response_code(200);
    }

    public function addToCart()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $price = $data['price'];
        $image = $data['image'];
        $productId = $data['product_id'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemFound = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] === $productId) {
                $item['quantity']++;
                $itemFound = true;
                break;
            }
        }

        if (!$itemFound) {
            $_SESSION['cart'][] = [
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => 1,
                'product_id' => $productId
            ];
        }

        echo json_encode(['status' => 'success']);
        http_response_code(201);
    }

    public function updateCart()
    {
        if (!isset($_POST['product_id'], $_POST['quantity'])) {
            echo json_encode(['message' => 'Невірні дані']);
            http_response_code(400);
            exit;
        }

        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }

        echo json_encode(['message' => 'Кошик оновлено', 'cart' => $_SESSION['cart']]);
        http_response_code(200);
        exit;
    }

    public function removeFromCart()
    {
        if (!isset($_POST['product_id'])) {
            echo json_encode(['message' => 'Невірний запит']);
            http_response_code(400);
            exit;
        }

        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]);

        $_SESSION['cart'] = array_values($_SESSION['cart']);

        echo json_encode(['message' => 'Товар видалено', 'cart' => $_SESSION['cart']]);
        http_response_code(200);
        exit;
    }

    public function placeOrder()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['message' => 'Не авторизовано']);
            http_response_code(401);
            exit;
        }

        if (empty($_SESSION['cart'])) {
            echo json_encode(['message' => 'Кошик порожній']);
            http_response_code(400);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $totalPrice = 0;
        $cartItems = $_SESSION['cart'];

        $inputData = json_decode(file_get_contents('php://input'), true);

        if (!isset($inputData['products']) || empty($inputData['products'])) {
            echo json_encode(['message' => 'Не вказано продукти']);
            http_response_code(400);
            exit;
        }

        $productIds = array_map(fn($p) => (int)$p['product_id'], $inputData['products']);

        foreach ($cartItems as $item) {
            if (in_array((int)$item['product_id'], $productIds)) {
                $totalPrice += $item['price'] * $item['quantity'];
            }
        }

        $address = $inputData['address'];
        $city = $inputData['city'];
        $postalCode = $inputData['postal_code'];

        $orderModel = new Order();
        $orderId = $orderModel->createOrder($userId, $totalPrice, $address, $city, $postalCode);

        foreach ($cartItems as $item) {
            if (in_array((int)$item['product_id'], $productIds)) {
                $orderModel->addOrderItem($orderId, (int)$item['product_id'], $item['quantity']);
            }
        }

        $_SESSION['cart'] = [];

        echo json_encode(['message' => 'Замовлення оформлено успішно', 'order_id' => $orderId]);
        http_response_code(201);
    }
}
