<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] === 'add') {
        $name = $data['name'];
        $price = (float)$data['price'];
        $image = $data['image'];

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] === $name) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'quantity' => 1
            ];
        }

        echo json_encode(["status" => "success"]);
        exit;
    }
}

if ($_GET['action'] === 'get') {
    echo json_encode($_SESSION['cart']);
    exit;
}
?>
