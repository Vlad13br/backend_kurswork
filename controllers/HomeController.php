<?php

require_once '../models/Product.php';
require_once '../models/Category.php';

class HomeController {
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort = $_GET['sort'] ?? 'newest';
        $limit = 6;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $category = $_GET['category'] ?? null;

        $productModel = new Product();
        $categoryModel = new Category();

        $products = $productModel->getAllProducts($page, $limit, $sort, $minPrice, $maxPrice, $category);

        $categories = $categoryModel->getAllCategories();

        $totalProducts = $productModel->getTotalProductCount($minPrice, $maxPrice, $category);
        $totalPages = ceil($totalProducts / $limit);

        if ($page < 1) {
            $page = 1;
        } elseif ($page > $totalPages) {
            $page = $totalPages;
        }

        $additionalScripts = '/scripts/cart/cart.js';

        require '../views/home.php';
    }
}
