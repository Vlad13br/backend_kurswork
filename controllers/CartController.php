<?php

class CartController
{
    public function showCart()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        echo json_encode(['cart' => $_SESSION['cart']]);
    }

    public function addToCart()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'];
        $price = $data['price'];
        $image = $data['image'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemFound = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] === $name) {
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
                'quantity' => 1
            ];
        }

        echo json_encode(['status' => 'success']);
    }

    public function updateCart()
    {
        if (!isset($_POST['product_id'], $_POST['quantity'])) {
            echo json_encode(['message' => 'Невірні дані']);
            exit;
        }

        $productId = $_POST['product_id'];
        $quantity = (int) $_POST['quantity'];

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']);

        echo json_encode(['message' => 'Кошик оновлено', 'cart' => $_SESSION['cart']]);
        exit;
    }

    public function removeFromCart()
    {
        if (!isset($_POST['product_id'])) {
            echo json_encode(['message' => 'Невірний запит']);
            exit;
        }

        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]);

        $_SESSION['cart'] = array_values($_SESSION['cart']);

        echo json_encode(['message' => 'Товар видалено', 'cart' => $_SESSION['cart']]);
        exit;
    }
}
