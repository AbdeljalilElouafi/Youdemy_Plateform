<?php

namespace App\Model;

use Config\Database;


class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllCategories() {
        $query = "SELECT id, name FROM categories WHERE 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}


?>