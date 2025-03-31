<?php

require_once '../core/Database.php';

class Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserOrders($userId)
    {
        $query = "
    SELECT o.id AS order_id, o.created_at, o.status, o.total_price, oi.product_id, oi.quantity, oi.price AS item_price, p.name AS product_name
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = :user_id
    ORDER BY o.created_at DESC
    ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $groupedOrders = [];
        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            if (!isset($groupedOrders[$orderId])) {
                $groupedOrders[$orderId] = [
                    'order_id' => $order['order_id'],
                    'created_at' => $order['created_at'],
                    'status' => $order['status'],
                    'total_price' => $order['total_price'],
                    'items' => []
                ];
            }
            $groupedOrders[$orderId]['items'][] = [
                'product_name' => $order['product_name'],
                'item_price' => $order['item_price'],
                'quantity' => $order['quantity']
            ];
        }

        return $groupedOrders;
    }


    public function createOrder($userId, $totalPrice, $address, $city, $postalCode)
    {

        $query = "INSERT INTO orders (user_id, total_price, address, city, postal_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $totalPrice, $address, $city, $postalCode]);

        return $this->db->lastInsertId();
    }


    public function addOrderItem($orderId, $productId, $quantity)
    {
        $query = "SELECT price, discount FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $finalPrice = $product['price'] - ($product['price'] * ($product['discount'] / 100));

        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$orderId, $productId, $quantity, $finalPrice]);
    }

    public function getAllOrders()
    {
        $stmt = $this->db->prepare(
            "SELECT o.id AS order_id,o.user_id, o.total_price, o.status, o.address, o.city, o.postal_code, o.tracking_number, o.created_at,
                oi.product_id, oi.quantity, oi.price, p.name AS product_name, p.description AS product_description, p.price AS product_price,
                u.first_name AS user_first_name, u.last_name AS user_last_name, u.email AS user_email, p.stock As product_stock 
         FROM orders o
         JOIN order_items oi ON o.id = oi.order_id
         JOIN products p ON oi.product_id = p.id
         JOIN users u ON o.user_id = u.id
         WHERE o.status NOT IN ('delivered', 'cancelled')"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateOrderStatus($orderId, $status)
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $orderId);
            $stmt->execute();

            if ($status === 'delivered') {
                $stmt = $this->db->prepare("
                SELECT product_id, quantity FROM order_items WHERE order_id = :orderId
            ");
                $stmt->bindParam(':orderId', $orderId);
                $stmt->execute();
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $stmt = $this->db->prepare("
                    SELECT stock FROM products WHERE id = :productId
                ");
                    $stmt->bindParam(':productId', $item['product_id']);
                    $stmt->execute();
                    $stock = $stmt->fetchColumn();

                    if ($stock === false) {
                        throw new Exception("Товар з ID {$item['product_id']} не знайдено.");
                    }

                    if ($stock < $item['quantity']) {
                        throw new Exception("Недостатньо товару (ID {$item['product_id']}) на складі.");
                    }

                    $stmt = $this->db->prepare("
                    UPDATE products SET stock = stock - :quantity WHERE id = :productId
                ");
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':productId', $item['product_id']);
                    $stmt->execute();
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    public function deleteOrder($orderId)
    {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
    }

}

?>
