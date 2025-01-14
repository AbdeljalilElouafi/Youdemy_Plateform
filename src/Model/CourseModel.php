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

    public function createCourse($data) {
        return $this->insertRecord($this->table, $data);
    }

    public function updateCourse($id, $data) {
        return $this->updateRecord($this->table, $data, $id);
    }

    public function deleteCourse($id) {
        return $this->deleteRecord($this->table, $id);
    }

    public function getTeacherCourses($teacherId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} WHERE teacher_id = ? LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCourseStatistics($teacherId) {
        $sql = "SELECT 
                    c.id,
                    c.title,
                    COUNT(DISTINCT e.student_id) as student_count,
                    COUNT(CASE WHEN e.status = 'completed' THEN 1 END) as completed_count
                FROM courses c
                LEFT JOIN enrollments e ON c.id = e.course_id
                WHERE c.teacher_id = ?
                GROUP BY c.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getStudentCourses($studentId) {
        $sql = "SELECT c.*, e.status as enrollment_status, e.enrolled_at
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function enrollStudent($courseId, $studentId) {
        $sql = "INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$courseId, $studentId]);
    }
}