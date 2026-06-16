<?php
require_once __DIR__ . '/interfaces/MenuRepositoryInterface.php';

class Menu implements MenuRepositoryInterface {
    private $conn;
    private $table_name = "menus";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAllMenus() {
        $query = "SELECT m.id, m.name, m.price, m.image, c.name as category_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN categories c ON m.category_id = c.id
                  ORDER BY c.id, m.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY type, name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?>
