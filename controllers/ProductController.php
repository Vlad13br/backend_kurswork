<?php

require_once '../vendor/autoload.php';
require_once '../models/Product.php';
require_once '../models/Category.php';
require_once '../models/Brand.php';

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
Configuration::instance($_ENV['CLOUDINARY_URL']);

class ProductController
{
    public function getProduct($params)
    {
        $productModel = new Product();

        try {
            $product = $productModel->getProductById($params['id']);
            $comments = $productModel->getProductComments($params['id']);
        } catch (Exception $e) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        if (!$product) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        $additionalScripts = '/scripts/product/product.js';
        $additionalScripts1 = '/scripts/cart/cart.js';

        http_response_code(200);
        header('Cache-Control: public, max-age=3600');
        require '../views/products/productPage.php';
    }

    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            $userId = $_SESSION['user_id'];

            if (!isset($productId, $rating, $comment, $userId)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Відсутні необхідні дані!'
                ]);
                return;
            }

            $productModel = new Product();
            $isAdded = $productModel->addComment($productId, $userId, $rating, $comment);

            if ($isAdded) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Коментар успішно додано!',
                    'comment' => [
                        'user' => 'Ви',
                        'rating' => $rating,
                        'text' => $comment,
                    ]
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Не вдалося додати коментар!'
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'status' => 'error',
                'message' => 'Невірний метод запиту!'
            ]);
        }
    }

    public function deleteComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['comment_id']) || !isset($_SESSION['user_id'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Невірний запит!']);
                return;
            }

            $commentId = $_POST['comment_id'];

            $productModel = new Product();
            $isDeleted = $productModel->deleteComment($commentId);

            if ($isDeleted) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Коментар видалено!']);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Помилка при видаленні!']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Невірний метод запиту!']);
        }
    }

    public function showProductForm()
    {
        $categoryModel = new Category();
        $brandModel = new Brand();

        try {
            $categories = $categoryModel->getAllCategories();
            $brands = $brandModel->getAllBrands();
        } catch (Exception $e) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        $additionalScripts = '/scripts/brand/brands.js';
        http_response_code(200);
        require '../views/products/create_product.php';
    }

    public function store()
    {
        $categoryId = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $brandId = $_POST['brand_id'];
        $discount = $_POST['discount'] ?? 0;

        $attributes = [];
        if (isset($_POST['attributes'])) {
            foreach ($_POST['attributes'] as $key => $value) {
                $attributes[] = [
                    'attribute_name' => $key,
                    'attribute_value' => $value,
                ];
            }
        }

        $images = $_FILES['images'] ?? [];

        $productModel = new Product();
        $productId = $productModel->createProduct($name, $description, $price, $stock, $categoryId, $brandId, $discount, $attributes);

        if ($productId) {
            $mainImageIndex = $_POST['main_image'] ?? 0;
            if (!empty($images['name'][0])) {
                $upload = new UploadApi();

                foreach ($images['tmp_name'] as $key => $tmpName) {
                    $uploadedFile = $upload->upload($tmpName);
                    $imageUrl = $uploadedFile['secure_url'];
                    $isMain = ($key == $mainImageIndex) ? true : false;

                    if (!$productModel->saveProductImage($productId, $imageUrl, $isMain)) {
                        http_response_code(400);
                        echo "Помилка при додаванні зображення.";
                        exit;
                    }
                }
            }

            http_response_code(201);
            header('Location: /');
            exit;
        } else {
            http_response_code(400);
            echo "Помилка при створенні продукту.";
        }
    }

    public function getCategoryAttributes()
    {
        if (isset($_GET['category_id'])) {
            $categoryId = $_GET['category_id'];

            $categoryModel = new Category();
            $attributes = $categoryModel->getCategoryAttributes($categoryId);

            if ($attributes) {
                http_response_code(200);
                echo json_encode($attributes);
            } else {
                http_response_code(404);
                echo json_encode([]);
            }
        } else {
            http_response_code(400);
            echo json_encode([]);
        }
    }

    public function editProduct($params)
    {
        $productModel = new Product();
        try {
            $product = $productModel->getProductToUpdateById($params['id']);
        } catch (Exception $e) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        if (!$product) {
            http_response_code(404);
            include '../views/404.php';
            exit;
        }

        http_response_code(200);
        require '../views/products/edit_product.php';
    }

    public function updateProduct($params)
    {
        $productId = $params['id'];
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $discount = $_POST['discount'] ?? 0;

        if (empty($name) || empty($description) || empty($price) || empty($stock)) {
            http_response_code(400);
            return;
        }

        $productModel = new Product();
        $updated = $productModel->updateProduct($productId, $name, $description, $price, $stock, $discount);

        if ($updated) {
            http_response_code(200);
            header('Location: /product/' . $productId);
            exit;
        } else {
            http_response_code(400);
            echo "Error updating product.";
        }
    }
}