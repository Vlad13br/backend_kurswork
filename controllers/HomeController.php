<?php

require_once '../models/Product.php';
require_once '../models/Category.php';

class HomeController
{
    public function index()
    {
        $sort = $_GET['sort'] ?? 'newest';
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $category = $_GET['category'] ?? null;

        $productModel = new Product();
        $categoryModel = new Category();

        try {
            $products = $productModel->getAllProducts($sort, $minPrice, $maxPrice, $category);
            $categories = $categoryModel->getAllCategories();
        } catch (Exception $e) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        $additionalScripts = '/scripts/cart/cart.js';
        $additionalScripts1 = '/scripts/home/home.js';

        http_response_code(200);
        header('Cache-Control: public, max-age=3600');
        require '../views/home.php';
    }
    public function fetch()
    {
        $sort = $_GET['sort'] ?? 'newest';
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $category = $_GET['category'] ?? null;

        $productModel = new Product();

        try {
            $products = $productModel->getAllProducts( $sort, $minPrice, $maxPrice, $category);
            echo json_encode([
                'products' => $products,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }
    }
}
