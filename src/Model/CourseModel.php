<?php
namespace App\Model;

class CourseModel extends BaseModel {
    protected $table = 'courses';

    public function getCourses($page = 1, $limit = 6, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE title LIKE ? OR description LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getCourses: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCourses($search = '') {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE title LIKE ? OR description LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm];
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error in getTotalCourses: " . $e->getMessage());
            return 0;
        }
    }
}