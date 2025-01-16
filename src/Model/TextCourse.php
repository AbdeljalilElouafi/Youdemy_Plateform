<?php

namespace App\Model;


 
class TextCourse extends CourseModel {
    public function addContent($courseData, $tagIds = []) {
        try {
            $this->db->beginTransaction();
            
            $slug = $this->generateSlug($courseData['title']);
            
            $sql = "INSERT INTO {$this->table} (
                title, slug, description, teacher_id, 
                category_id, status, content_type, content_text
            ) VALUES (?, ?, ?, ?, ?, ?, 'text', ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $courseData['title'],
                $slug,
                $courseData['description'],
                $courseData['teacher_id'],
                $courseData['category_id'],
                $courseData['status'],
                $courseData['content']
            ]);
            
            $courseId = $this->db->lastInsertId();
            
            if (!empty($tagIds)) {
                $this->createCourseTags($courseId, $tagIds);
            }
            
            $this->db->commit();
            return $courseId;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}