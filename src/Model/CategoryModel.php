<?php

namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

class CategoryModel extends BaseModel {
    private $table = 'categories';
    

    protected $db;
    public $id;
    public $name;
    public $description;
    public $slug;
    public $created_at;
    public $updated_at;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
    }



    // Create new category
    public function addCategory($data) {
        try {
            // Generate slug from name
            $data['slug'] = $this->generateSlug($data['name']);
            return $this->insertRecord($this->table, $data);
        } catch(PDOException $e) {
            error_log("Error in addCategory: " . $e->getMessage());
            return false;
        }
    }

    // Update category
    public function editCategory($id, $data) {
        try {
            if (isset($data['name'])) {
                $data['slug'] = $this->generateSlug($data['name'], $id);
            }
            return $this->updateRecord($this->table, $data, $id);
        } catch(PDOException $e) {
            error_log("Error in editCategory: " . $e->getMessage());
            return false;
        }
    }

    // Delete category
    public function deleteCategory($id) {
        return $this->deleteRecord($this->table, $id);
    }

    public function getAllCategories() {
        $query = "SELECT id, name FROM categories WHERE 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $this->fetchRecords($this->table, [], 'created_at DESC');
    }

    public function getAlCategories() {
        return $this->fetchRecords($this->table, [], 'created_at DESC');
    }

    public function getCategoryById($id) {
        $conditions = ['id' => $id];
        $results = $this->fetchRecords($this->table, $conditions);
        return $results ? $results[0] : null;
    }

    public function getCategoryBySlug($slug) {
        $conditions = ['slug' => $slug];
        $results = $this->fetchRecords($this->table, $conditions);
        return $results ? $results[0] : null;
    }

    private function generateSlug($name, $excludeId = null) {
        
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        
        $params = [$slug];
        if ($excludeId) {
            $params[] = $excludeId;
        }
        
        $stmt->execute($params);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            $i = 1;
            do {
                $newSlug = $slug . '-' . $i;
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ?");
                $stmt->execute([$newSlug]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                $i++;
            } while ($count > 0);
            $slug = $newSlug;
        }
        
        return $slug;
    }
    public function countCategories() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch(PDOException $e) {
            error_log("Error in countCategories: " . $e->getMessage());
            return 0;
        }
    }

    public function getCategoriesWithCounts() {
        try {
            $sql = "SELECT c.*, COUNT(co.id) as courses_count 
                   FROM {$this->table} c 
                   LEFT JOIN courses co ON c.id = co.category_id 
                   GROUP BY c.id 
                   ORDER BY c.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getCategoriesWithCounts: " . $e->getMessage());
            return [];
        }
    }
}

?>