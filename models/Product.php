<?php

require_once '../core/Database.php';

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllProducts($page = 1, $limit = 12, $sort = 'newest', $minPrice = null, $maxPrice = null, $category = null)
    {
        $offset = ($page - 1) * $limit;

        $orderBy = "p.created_at DESC";

        if ($sort === 'price_asc') {
            $orderBy = "p.price ASC";
        } elseif ($sort === 'price_desc') {
            $orderBy = "p.price DESC";
        } elseif ($sort === 'popular') {
            $orderBy = "p.sold DESC";
        }

        $whereClauses = [];
        if ($minPrice) {
            $whereClauses[] = "(p.price * (1 - p.discount)) >= :min_price";
        }
        if ($maxPrice) {
            $whereClauses[] = "(p.price * (1 - p.discount)) <= :max_price";
        }
        if ($category) {
            $whereClauses[] = "p.category_id = :category";
        }

        $whereSql = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

        $stmt = $this->db->prepare("
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
        $whereSql
        ORDER BY p.stock = 0, $orderBy
        LIMIT :limit OFFSET :offset
    ");

        if ($minPrice) $stmt->bindParam(':min_price', $minPrice, PDO::PARAM_INT);
        if ($maxPrice) $stmt->bindParam(':max_price', $maxPrice, PDO::PARAM_INT);
        if ($category) $stmt->bindParam(':category', $category, PDO::PARAM_INT);

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProductCount($minPrice = null, $maxPrice = null, $category = null) {
        $query = "SELECT COUNT(*) FROM products p";

        $params = [];

        if ($minPrice !== null) {
            $query .= " WHERE p.price >= :min_price";
            $params[':min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $query .= $minPrice !== null ? " AND p.price <= :max_price" : " WHERE p.price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        if ($category !== null) {
            $query .= ($minPrice !== null || $maxPrice !== null) ? " AND p.category_id = :category" : " WHERE p.category_id = :category";
            $params[':category'] = $category;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchColumn();
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

    public function getProductComments($productId)
    {
        $stmt = $this->db->prepare("
        SELECT r.id, r.rating, r.comment, r.created_at, u.first_name, u.last_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.product_id = :product_id
        ORDER BY r.created_at DESC
    ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($productId, $userId, $rating, $comment)
    {
        $stmt = $this->db->prepare("
        INSERT INTO reviews (product_id, user_id, rating, comment, created_at)
        VALUES (:product_id, :user_id, :rating, :comment, NOW())
    ");
        $stmt->execute([
            ':product_id' => $productId,
            ':user_id' => $userId,
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        return true;
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
