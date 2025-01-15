<?php

namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

class TagModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllTags() {
        $query = "SELECT id, name FROM tags WHERE 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createCourseTags($courseId, array $tagIds) {
        $query = "INSERT INTO course_tags (course_id, tag_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        
        foreach ($tagIds as $tagId) {
            $stmt->execute([$courseId, $tagId]);
        }
        
        return true;
    }
}


?>