<?php

require_once '../core/Database.php';

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createCategory($name)
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);

        return $this->db->lastInsertId();
    }

    public function addAttributes($categoryId, $attributes)
    {
        foreach ($attributes as $attributeName) {
            $stmt = $this->db->prepare("INSERT INTO category_attributes (category_id, attribute_name) VALUES (:category_id, :attribute_name)");
            $stmt->execute(['category_id' => $categoryId, 'attribute_name' => $attributeName]);
        }
    }

    public function getAllCategories()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCategoriesAndAtributes()
    {
        $sql = "SELECT c.id, c.name, 
                   GROUP_CONCAT(a.attribute_name SEPARATOR ', ') AS attributes
            FROM categories c
            LEFT JOIN category_attributes a ON c.id = a.category_id
            GROUP BY c.id, c.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as &$category) {
            $category['attributes'] = $category['attributes'] ? explode(', ', $category['attributes']) : [];
        }

        return $categories;
    }


    public function getCategoryAttributes($categoryId)
    {
        $stmt = $this->db->prepare("SELECT attribute_name FROM category_attributes WHERE category_id = :category_id");
        $stmt->execute(['category_id' => $categoryId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
