<?php

namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

class TagModel {
    private $table = 'tags';
    
    
    protected $db;
    public $id;
    public $name;
    public $description;
    public $slug;
    public $created_at;
    public $updated_at;


    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addTag($data) {
        try {

            $data['slug'] = $this->generateSlug($data['name']);
            return $this->insertRecord($this->table, $data);
        } catch(PDOException $e) {
            error_log("Error in addTag: " . $e->getMessage());
            return false;
        }
    }


    public function editTag($id, $data) {
        try {
            if (isset($data['name'])) {
                $data['slug'] = $this->generateSlug($data['name'], $id);
            }
            return $this->updateRecord($this->table, $data, $id);
        } catch(PDOException $e) {
            error_log("Error in editTag: " . $e->getMessage());
            return false;
        }
    }


    public function deleteTag($id) {
        return $this->deleteRecord($this->table, $id);
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