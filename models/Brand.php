<?php

require_once '../core/Database.php';

class Brand
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllBrands()
    {
        $stmt = $this->db->query("SELECT * FROM brands");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBrand($name)
    {
        $stmt = $this->db->prepare("INSERT INTO brands (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);

        return $this->db->lastInsertId();
    }
}
?>
