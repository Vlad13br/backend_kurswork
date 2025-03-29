<?php

require_once '../models/Product.php';

class HomeController {
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;

        $productModel = new Product();
        $products = $productModel->getAllProducts($page, $limit);

        $totalProducts = $productModel->getTotalProductCount();
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
