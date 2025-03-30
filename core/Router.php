<?php
require '../vendor/autoload.php';
require_once '../controllers/HomeController.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/CategoryController.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/BrandController.php';
require_once '../controllers/ProfileController.php';
require_once  '../controllers/CartController.php';
require_once  '../controllers/AdminController.php';

class Router
{
    private $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
        $this->mapRoutes();
    }

    private function mapRoutes()
    {
        $this->router->map('GET', '/', 'HomeController#index');
        $this->router->map('GET', '/home', 'HomeController#index');
        $this->router->map('GET', '/product/[i:id]', 'ProductController#getProduct');
        $this->router->map('POST', '/add-comment', 'ProductController#addComment');

        $this->router->map('GET', '/register', 'AuthController#showRegisterForm');
        $this->router->map('POST', '/register/store', 'AuthController#register');
        $this->router->map('GET', '/login', 'AuthController#showLoginForm');
        $this->router->map('POST', '/login/store', 'AuthController#login');
        $this->router->map('GET', '/logout', 'AuthController#logout');

        $this->router->map('GET', '/profile', 'ProfileController#showProfile');
        $this->router->map('POST', '/update-profile', 'ProfileController#updateProfile');
        $this->router->map('POST', '/change-password', 'ProfileController#changePassword');

        $this->router->map('GET', '/get-cart', 'CartController#showCart');
        $this->router->map('POST', '/add-to-cart', 'CartController#addToCart');
        $this->router->map('POST', '/update-cart', 'CartController#updateCart');
        $this->router->map('POST', '/remove-from-cart', 'CartController#removeFromCart');
        $this->router->map('POST', '/place-order', 'CartController#placeOrder');

        $this->router->map('GET', '/admin', 'AdminController#showProfile');
        $this->router->map('POST', '/admin/updateOrderStatus', 'AdminController#updateOrderStatus');
        $this->router->map('POST', '/admin/deleteOrder', 'AdminController#deleteOrder');

        $this->router->map('GET', '/add-category', 'CategoryController#showCategoryForm');
        $this->router->map('POST', '/add-category/store', 'CategoryController#createCategory');

        $this->router->map('GET', '/add-brand', 'BrandController#showBrandForm');
        $this->router->map('POST', '/add-brand/store', 'BrandController#createBrand');

        $this->router->map('GET', '/add-product', 'ProductController#showProductForm');
        $this->router->map('POST', '/store-product', 'ProductController#store');
        $this->router->map('GET', '/get-category-attributes', 'ProductController#getCategoryAttributes');

        $this->router->map('GET', '/edit-product/[i:id]', 'ProductController#editProduct');
        $this->router->map('POST', '/edit-product/[i:id]', 'ProductController#updateProduct');


    }

    public function direct($uri)
    {
        $match = $this->router->match($uri);

        if ($match) {
            list($controllerName, $method) = explode('#', $match['target']);

            $controller = new $controllerName();

            $controller->$method($match['params']);
        } else {
            include '../views/404.php';
        }
    }
}
