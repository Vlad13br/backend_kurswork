<?php

require_once '../core/Database.php';

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllProducts()
    {
        $stmt = $this->db->query("
        SELECT 
            p.id AS product_id, 
            p.name AS product_name, 
            p.description AS product_description, 
            p.price AS product_price, 
            p.stock AS product_stock, 
            p.discount AS product_discount, 
            p.created_at AS product_created_at, 
            c.name AS category, 
            b.name AS brand,
            (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) AS main_image
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN brands b ON p.brand_id = b.id
        ORDER BY p.id;
    ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($product_id)
    {
        $stmt = $this->db->prepare("
    SELECT p.id, p.name, p.description, p.price AS product_price, p.stock, p.discount, 
        b.name AS brand, 
        GROUP_CONCAT(pi.image_url SEPARATOR ', ') AS images, 
        GROUP_CONCAT(pa.attribute_name SEPARATOR ', ') AS attribute_name, 
        GROUP_CONCAT(pa.attribute_value SEPARATOR ', ') AS attribute_value
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN product_images pi ON pi.product_id = p.id
    LEFT JOIN product_attributes pa ON pa.product_id = p.id
    WHERE p.id = :id
    GROUP BY p.id
");

        $stmt->execute(['id' => $product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product['attribute_name'] && $product['attribute_value']) {
            $attributes = explode(', ', $product['attribute_name']);
            $attribute_values = explode(', ', $product['attribute_value']);

            $uniqueAttributes = array_unique($attributes);
            $uniqueAttributeValues = array_unique($attribute_values);

            $product['attributes'] = array_map(function ($attr, $value) {
                return ['attribute_name' => $attr, 'attribute_value' => $value];
            }, $uniqueAttributes, $uniqueAttributeValues);
        } else {
            $product['attributes'] = [];
        }


        if ($product['images']) {
            $product['images'] = explode(', ', $product['images']);
        }

        return $product;
    }

    public function getProductToUpdateById($product_id)
    {
        $stmt = $this->db->prepare("SELECT * from products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "Product not found!";
        }

        return $product;
    }


    public function createProduct($name, $description, $price, $stock, $categoryId, $brandId, $discount, $attributes)
    {
        $stmt = $this->db->prepare("
        INSERT INTO products (name, description, price, stock, category_id, brand_id, discount)
        VALUES (:name, :description, :price, :stock, :category_id, :brand_id, :discount)
    ");

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':category_id' => $categoryId,
            ':brand_id' => $brandId,
            ':discount' => $discount,
        ]);

        $productId = $this->db->lastInsertId();

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $stmt = $this->db->prepare("
        INSERT INTO product_attributes (product_id, attribute_name, attribute_value)
        VALUES (:product_id, :attribute_name, :attribute_value)
    ");
                $stmt->execute([
                    ':product_id' => $productId,
                    ':attribute_name' => $attribute['attribute_name'],
                    ':attribute_value' => $attribute['attribute_value'],
                ]);
            }

        }

        return $productId;
    }

    public function saveProductImage($productId, $imageUrl, $isMain)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE id = :product_id");
        $stmt->execute(['product_id' => $productId]);
        $productExists = $stmt->fetchColumn();

        if (!$productExists) {
            throw new Exception("Продукт з id $productId не існує.");
        }

        $stmt = $this->db->prepare("
        INSERT INTO product_images (product_id, image_url, is_main)
        VALUES (:product_id, :image_url, :is_main)
    ");
        $stmt->execute([
            ':product_id' => $productId,
            ':image_url' => $imageUrl,
            ':is_main' => $isMain ? 1 : 0
        ]);

        return true;
    }

    public function updateProduct($productId, $name, $description, $price, $stock, $discount)
    {
        $stmt = $this->db->prepare("
        UPDATE products
        SET name = :name, description = :description, price = :price, stock = :stock, discount = :discount
        WHERE id = :product_id
    ");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':discount' => $discount,
            ':product_id' => $productId
        ]);

        return true;
    }


}

?>
