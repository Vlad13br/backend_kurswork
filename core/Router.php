<?php
require '../vendor/autoload.php';
require_once '../controllers/HomeController.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/CategoryController.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/BrandController.php';
require_once '../controllers/ProfileController.php';
require_once '../controllers/CartController.php';
require_once '../controllers/AdminController.php';

require_once '../middlewares/AuthMiddleware.php';
require_once '../middlewares/AdminMiddleware.php';

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

        $this->router->map('POST', '/add-comment', 'ProductController#addComment', 'auth_add_comment');

        $this->router->map('GET', '/register', 'AuthController#showRegisterForm');
        $this->router->map('POST', '/register/store', 'AuthController#register');
        $this->router->map('GET', '/login', 'AuthController#showLoginForm');
        $this->router->map('POST', '/login/store', 'AuthController#login');
        $this->router->map('GET', '/logout', 'AuthController#logout');

        $this->router->map('GET', '/profile', 'ProfileController#showProfile', 'auth_profile');
        $this->router->map('POST', '/update-profile', 'ProfileController#updateProfile', 'auth_update_profile');
        $this->router->map('POST', '/change-password', 'ProfileController#changePassword', 'auth_change_password');

        $this->router->map('GET', '/get-cart', 'CartController#showCart');
        $this->router->map('POST', '/add-to-cart', 'CartController#addToCart');
        $this->router->map('POST', '/update-cart', 'CartController#updateCart');
        $this->router->map('POST', '/remove-from-cart', 'CartController#removeFromCart');

        $this->router->map('POST', '/place-order', 'CartController#placeOrder', 'auth_place_order');

        $this->router->map('GET', '/admin', 'AdminController#showProfile', 'admin_dashboard');
        $this->router->map('POST', '/admin/updateOrderStatus', 'AdminController#updateOrderStatus', 'admin_update_order');
        $this->router->map('POST', '/admin/deleteOrder', 'AdminController#deleteOrder', 'admin_delete_order');
        $this->router->map('POST', '/delete-comment', 'ProductController#deleteComment', 'admin_delete_comment');
        $this->router->map('GET', '/add-category', 'CategoryController#showCategoryForm', 'admin_add_category');
        $this->router->map('POST', '/add-category/store', 'CategoryController#createCategory', 'admin_store_category');
        $this->router->map('GET', '/add-brand', 'BrandController#showBrandForm', 'admin_add_brand');
        $this->router->map('POST', '/add-brand/store', 'BrandController#createBrand', 'admin_store_brand');
        $this->router->map('GET', '/add-product', 'ProductController#showProductForm', 'admin_add_product');
        $this->router->map('POST', '/store-product', 'ProductController#store', 'admin_store_product');
        $this->router->map('GET', '/get-category-attributes', 'ProductController#getCategoryAttributes', 'admin_get_category_attributes');
        $this->router->map('GET', '/edit-product/[i:id]', 'ProductController#editProduct', 'admin_edit_product');
        $this->router->map('POST', '/edit-product/[i:id]', 'ProductController#updateProduct', 'admin_update_product');
    }

    public function direct($uri)
    {
        $match = $this->router->match($uri);

        if ($match) {
            list($controllerName, $method) = explode('#', $match['target']);

            if (isset($match['name'])) {
                if (str_starts_with($match['name'], 'auth')) {
                    AuthMiddleware::handle();
                } elseif (str_starts_with($match['name'], 'admin')) {
                    AdminMiddleware::handle();
                }
            }

            $controller = new $controllerName();
            $controller->$method($match['params']);
        } else {
            include '../views/404.php';
        }
    }
}