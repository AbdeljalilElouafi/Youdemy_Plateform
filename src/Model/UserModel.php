<?php

namespace App\Model;

class UserModel extends AbstractModel {
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }

    public function register($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password, first_name, last_name, role) 
             VALUES (:email, :password, :firstName, :lastName, :role)"
        );

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute($data);
    }

    public function countTeachers() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'teacher'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function countStudents() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'student'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getTopTeachers() {
        $sql = "SELECT u.*, COUNT(c.id) as course_count, COUNT(DISTINCT e.student_id) as student_count 
                FROM users u 
                LEFT JOIN courses c ON u.id = c.teacher_id 
                LEFT JOIN enrollments e ON c.id = e.course_id 
                WHERE u.role = 'teacher' 
                GROUP BY u.id 
                ORDER BY student_count DESC 
                LIMIT 3";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare(
            "UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id"
        );
        return $stmt->execute(['id' => $userId]);
    }


    public function checkAuth() {
            if (!isset($_SESSION['user'])) {
                header('Location: /login.php');
                exit;
            }
        }

    public function checkTeacherRole() {
            checkAuth();
            if ($_SESSION['user']['role'] !== 'teacher') {
                header('Location: /');
                exit;
            }
        }

    public function checkStudentRole() {
            checkAuth();
            if ($_SESSION['user']['role'] !== 'student') {
                header('Location: /');
                exit;
            }
        }


        public function getAllUsers($role = null, $status = null) {
            $sql = "SELECT * FROM users WHERE role != 'admin'";
            $params = [];
            
            if ($role) {
                $sql .= " AND role = :role";
                $params['role'] = $role;
            }
            
            if ($status) {
                $sql .= " AND status = :status";
                $params['status'] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        public function updateUserStatus($userId, $status) {
            $stmt = $this->db->prepare(
                "UPDATE users SET status = :status WHERE id = :id AND role != 'admin'"
            );
            return $stmt->execute([
                'id' => $userId,
                'status' => $status
            ]);
        }
        
        public function deleteUser($userId) {
            $stmt = $this->db->prepare(
                "UPDATE users SET status = 'deleted' WHERE id = :id AND role != 'admin'"
            );
            return $stmt->execute(['id' => $userId]);
        }

}