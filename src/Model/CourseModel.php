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

    public function getCourseWithTags($slug) {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(t.id) as tag_ids,
                GROUP_CONCAT(t.name) as tag_names
                FROM {$this->table} c
                LEFT JOIN course_tags ct ON c.id = ct.course_id
                LEFT JOIN tags t ON ct.tag_id = t.id
                WHERE c.slug = ?
                GROUP BY c.id";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$slug]);
            $course = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($course) {
                // Convert tag strings to arrays
                $course['tags'] = [];
                if ($course['tag_ids']) {
                    $tagIds = explode(',', $course['tag_ids']);
                    $tagNames = explode(',', $course['tag_names']);
                    foreach ($tagIds as $i => $id) {
                        $course['tags'][] = [
                            'id' => $id,
                            'name' => $tagNames[$i]
                        ];
                    }
                }
                unset($course['tag_ids'], $course['tag_names']);
            }
            
            return $course;
        } catch (\PDOException $e) {
            error_log("Error in getCourseWithTags: " . $e->getMessage());
            return null;
        }
    }
    
    public function deleteCourseTags($courseId) {
        $sql = "DELETE FROM course_tags WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$courseId]);
    }
    
    public function updateCourse($id, $data) {
        $allowedFields = [
            'title', 'description', 'category_id', 'status',
            'content_type', 'content_url', 'content_text'
        ];
        
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        $setClauses = [];
        $params = [];
        
        foreach ($updateData as $field => $value) {
            $setClauses[] = "`$field` = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Error in updateCourse: " . $e->getMessage());
            return false;
        }
    }
    
    public function getDB() {
        return $this->db;
    }

    public function deleteCourse($id) {
        return $this->deleteRecord($this->table, $id);
    }
 

    public function getTeacherStudents($teacherId) {
        $sql = "SELECT DISTINCT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    GROUP_CONCAT(c.title) as enrolled_courses,
                    COUNT(DISTINCT e.course_id) as courses_count,
                    MAX(e.enrolled_at) as last_enrollment_date
                FROM users u
                JOIN enrollments e ON u.id = e.student_id
                JOIN courses c ON e.course_id = c.id
                WHERE c.teacher_id = ? AND u.role = 'student'
                GROUP BY u.id, u.first_name, u.last_name, u.email
                ORDER BY last_enrollment_date DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$teacherId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getTeacherStudents: " . $e->getMessage());
            return [];
        }
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

    public function getTopCourses() {
        $sql = "SELECT c.*, COUNT(e.id) as enrollment_count, u.first_name, u.last_name 
                FROM courses c 
                LEFT JOIN enrollments e ON c.id = e.course_id 
                LEFT JOIN users u ON c.teacher_id = u.id 
                GROUP BY c.id 
                ORDER BY enrollment_count DESC 
                LIMIT 3";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
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
        $sql = "SELECT *
                FROM {$this->table} c
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function searchCourses($searchTerm = '', $page = 1, $limit = 6, $categoryId = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, u.first_name as teacher_name, u.last_name, cat.name as category_name,
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
    public function searchAllCourses($searchTerm = '', $page = 1, $limit = 6, $categoryId = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, u.first_name as teacher_name, u.last_name, cat.name as category_name,
                GROUP_CONCAT(t.name) as tags
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN course_tags ct ON c.id = ct.course_id
                LEFT JOIN tags t ON ct.tag_id = t.id";
        
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