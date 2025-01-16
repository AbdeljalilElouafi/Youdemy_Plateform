<?php
namespace App\Model;

abstract class CourseModel extends BaseModel {
    protected $table = 'courses';

    abstract public function addContent($courseData, $tagIds = []);

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
 
    public function getTeacherCourses($teacherId, $page = 1, $limit = 20) {
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

    public function getAvailableCourses($studentId) {
        $sql = "SELECT c.* 
                FROM courses c 
                WHERE c.status = 'published' 
                AND c.id NOT IN (
                    SELECT course_id FROM enrollments WHERE student_id = ?
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getCourseById($id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function getCourseBySlug($slug) {
        $sql = "SELECT * FROM courses WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function createCourseWithContent($courseData, $tagIds) {
        try {
            $this->db->beginTransaction();

            // Generate slug from title
            $slug = $this->generateSlug($courseData['title']);
            
            // Prepare course data
            $course = [
                'title' => $courseData['title'],
                'slug' => $slug,
                'description' => $courseData['description'],
                'teacher_id' => $courseData['teacher_id'],
                'category_id' => $courseData['category_id'],
                'status' => $courseData['status']
            ];


            $courseId = $this->createCourse($course);

            if (!$courseId) {
                throw new \Exception('Failed to create course');
            }

            // Insert course content
            $contentQuery = "INSERT INTO course_contents (course_id, title, content_type, content_url, duration, file_size, order_index) 
                           VALUES (?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $this->db->prepare($contentQuery);
            $stmt->execute([
                $courseId,
                $courseData['title'],
                $courseData['content']['type'],
                $courseData['content']['url'],
                $courseData['content']['duration'],
                $courseData['content']['file_size']
            ]);

            // Insert course tags
            if (!empty($tagIds)) {
                $this->createCourseTags($courseId, $tagIds);
            }

            $this->db->commit();
            return $courseId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in createCourseWithContent: " . $e->getMessage());
            return false;
        }
    }

    public function createCourseTags($courseId, array $tagIds) {
        $query = "INSERT INTO course_tags (course_id, tag_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        
        foreach ($tagIds as $tagId) {
            $stmt->execute([$courseId, $tagId]);
        }
    }

    public function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public function slugExists($slug) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() > 0;
    }


    public function getCourseWithContent($courseId) {
        $sql = "SELECT c.*, cc.content_type, cc.content_url, cc.duration, cc.file_size 
                FROM {$this->table} c
                LEFT JOIN course_contents cc ON c.id = cc.course_id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function searchCourses($searchTerm = '', $page = 1, $limit = 6, $categoryId = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, u.first_name, u.last_name, cat.name as category_name,
                GROUP_CONCAT(t.name) as tags
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN course_tags ct ON c.id = ct.course_id
                LEFT JOIN tags t ON ct.tag_id = t.id
                WHERE c.status = 'published'";
        
        $params = [];
        
        if ($searchTerm) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ? OR t.name LIKE ?)";
            $searchPattern = "%{$searchTerm}%";
            $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern]);
        }
        
        if ($categoryId) {
            $sql .= " AND c.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " GROUP BY c.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in searchCourses: " . $e->getMessage());
            return [];
        }
    }

    public function getAvailableCoursesForStudent($studentId, $searchTerm = '', $page = 1, $limit = 6, $categoryId = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, u.first_name, u.last_name, cat.name as category_name,
                GROUP_CONCAT(t.name) as tags
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN course_tags ct ON c.id = ct.course_id
                LEFT JOIN tags t ON ct.tag_id = t.id
                WHERE c.status = 'published'
                AND c.id NOT IN (
                    SELECT course_id FROM enrollments WHERE student_id = ?
                )";
        
        $params = [$studentId];
        
        if ($searchTerm) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ? OR t.name LIKE ?)";
            $searchPattern = "%{$searchTerm}%";
            $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern]);
        }
        
        if ($categoryId) {
            $sql .= " AND c.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " GROUP BY c.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getAvailableCoursesForStudent: " . $e->getMessage());
            return [];
        }
    }

    public function getEnrolledCoursesForStudent($studentId, $searchTerm = '', $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, e.status as enrollment_status, e.enrolled_at,
                e.progress_percentage, u.first_name, u.last_name
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                LEFT JOIN users u ON c.teacher_id = u.id
                WHERE e.student_id = ?";
        
        $params = [$studentId];
        
        if ($searchTerm) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $searchPattern = "%{$searchTerm}%";
            $params = array_merge($params, [$searchPattern, $searchPattern]);
        }
        
        $sql .= " ORDER BY e.enrolled_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getEnrolledCoursesForStudent: " . $e->getMessage());
            return [];
        }
    }

}