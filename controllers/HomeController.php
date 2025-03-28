<?php

require_once '../models/Product.php';

class HomeController {
    public function index() {

        $productModel = new Product();
        $products = $productModel->getAllProducts();
        require '../views/home.php';
    }
}
