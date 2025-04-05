<?php

require_once '../models/Category.php';

class CategoryController
{
    public function showCategoryForm()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllCategoriesAndAtributes();

        if ($categories === false) {
            http_response_code(500);
            require '../views/500.php';
            exit;
        }

        $additionalScripts = '/scripts/category/category.js';
        require '../views/category/add-category.php';
    }

    public function createCategory()
    {
        if (isset($_POST['category_name'])) {
            $categoryName = $_POST['category_name'];
            $attributeNames = $_POST['attribute_name'] ?? [];

            $category = new Category();

            $categoryId = $category->createCategory($categoryName);

            if ($categoryId) {
                $category->addAttributes($categoryId, $attributeNames);

                http_response_code(201);
                header("Location: /");
                exit;
            } else {
                http_response_code(500);
                require '../views/500.php';
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Не вдалося отримати назву категорії']);
            exit;
        }
    }
}
?>
