<?php

namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

class TagModel extends BaseModel {
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

            if ($this->tagExists($data['name'])) {
                error_log("Tag already exists: " . $data['name']);
                return false;  
            }


            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }


            return $this->insertRecord($this->table, $data);
        } catch(PDOException $e) {
            error_log("Error in addTag: " . $e->getMessage());
            return false;
        }
    }


    private function tagExists($tagName) {
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tagName]);
        return $stmt->fetchColumn() > 0;
    }

 
    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() > 0;
    }


    public function generateUniqueSlug($name) {

        $slug = $this->generateSlug($name);


        $counter = 1;
        $originalSlug = $slug;
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }


    public function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return $slug;
    }

    public function editTag($id, $data) {
        try {
            if (isset($data['name'])) {

                if (isset($data['slug']) && $this->slugExists($data['slug'])) {
                    error_log("Slug already exists: " . $data['slug']);
                    return false;
                }
                return $this->updateRecord($this->table, $data, $id);
            }
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
