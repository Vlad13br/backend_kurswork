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
        $product = $productModel->getProductById($params['id']);
        if (!$product) {
            include '../views/404.php';
            exit;
        }
        $comments = $productModel->getProductComments($params['id']);
        $additionalScripts = '/scripts/product/product.js';
        $additionalScripts1 = '/scripts/cart/cart.js';
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
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Відсутні необхідні дані!'
                ]);
                return;
            }

            $productModel = new Product();
            $isAdded = $productModel->addComment($productId, $userId, $rating, $comment);

            if ($isAdded) {
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
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Не вдалося додати коментар!'
                ]);
            }
        } else {
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
                echo json_encode(['status' => 'error', 'message' => 'Невірний запит!']);
                return;
            }

            $commentId = $_POST['comment_id'];

            $productModel = new Product();
            $isDeleted = $productModel->deleteComment($commentId);

            if ($isDeleted) {
                echo json_encode(['status' => 'success', 'message' => 'Коментар видалено!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Помилка при видаленні!']);
            }
        }
    }

    public function showProductForm()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllCategories();

        $brandModel = new Brand();
        $brands = $brandModel->getAllBrands();
        $additionalScripts = '/scripts/brand/brands.js';
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
                        echo "Помилка при додаванні зображення.";
                    }
                }
            }

            header('Location: /');
            exit;
        } else {
            echo "Помилка при створенні продукту.";
        }
    }

    public function getCategoryAttributes()
    {
        if (isset($_GET['category_id'])) {
            $categoryId = $_GET['category_id'];

            $categoryModel = new Category();
            $attributes = $categoryModel->getCategoryAttributes($categoryId);

            echo json_encode($attributes);
        } else {
            echo json_encode([]);
        }
    }

    public function editProduct($params)
    {
        $productModel = new Product();
        $product = $productModel->getProductToUpdateById($params['id']);

        if (!$product) {
            include '../views/404.php';
            exit;
        }

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
            return;
        }

        $productModel = new Product();
        $updated = $productModel->updateProduct($productId, $name, $description, $price, $stock, $discount);

        if ($updated) {
            header('Location: /product/' . $productId);
            exit;
        } else {
            echo "Error updating product.";
        }
    }


}
