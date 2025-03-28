<?php

require_once '../models/Brand.php';

class BrandController
{
    public function showBrandForm()
    {
        $brandModel = new Brand();
        $brands = $brandModel->getAllBrands();
        $additionalScripts = '/scripts/brand/brandForm.js';

        require '../views/brand/add-brand.php';
    }

    public function createBrand()
    {
        header("Content-Type: application/json");

        if (!isset($_POST['brand_name']) || empty(trim($_POST['brand_name']))) {
            echo json_encode(["error" => "Назва бренду не може бути порожньою."]);
            http_response_code(400);
            exit;
        }

        $brandName = trim($_POST['brand_name']);
        $brand = new Brand();

        $existingBrands = $brand->getAllBrands();
        foreach ($existingBrands as $existingBrand) {
            if (strcasecmp($existingBrand['name'], $brandName) === 0) {
                echo json_encode(["error" => "Такий бренд уже існує."]);
                http_response_code(400);
                exit;
            }
        }

        $brand->createBrand($brandName);
        echo json_encode(["success" => true]);
        http_response_code(200);
        exit;
    }

}
?>
