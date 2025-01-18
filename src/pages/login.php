<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\UserModel;

$error = '';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {

        $user = $userModel->authenticate($_POST['email'], $_POST['password']);
        
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ];

            if ($user['role'] === 'teacher') {
                header('Location: ../pages/teacher-page.php');
            } else if ($user['role'] === 'student') {
                header('Location: ../pages/student-page.php');
            } else {
                header('Location: ../pages/admin-dashboard.php');    }
                    exit;
            }
          
        $error = 'Invalid credentials';
    } elseif (isset($_POST['reg-email'])) {
        $result = $userModel->register([
            'email' => $_POST['reg-email'],
            'password' => $_POST['reg-password'],
            'firstName' => $_POST['first-name'],
            'lastName' => $_POST['last-name'],
            'role' => $_POST['role']
        ]);
        
        if ($result) {
            $success = 'Registration successful! Please login.';
        } else {
            $error = 'Registration failed';
        }
    }
}


require_once __DIR__ . '/../View/login.php';