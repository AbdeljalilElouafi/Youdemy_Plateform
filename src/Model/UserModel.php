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
}