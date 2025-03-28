<?php

require_once '../models/Category.php';

class CategoryController
{
    public function showCategoryForm()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllCategoriesAndAtributes();
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

            $category->addAttributes($categoryId, $attributeNames);

            header("Location: /");
            exit;
        } else {
            echo "Не вдалося отримати назву категорії.";
        }
    }
}
?>
