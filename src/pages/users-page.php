<?php
session_start();
require_once '../../vendor/autoload.php';


$userModel = new \App\Model\UserModel();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? '';
    
    if (!empty($userId)) {
        try {
            switch ($action) {
                case 'activate':
                    $userModel->updateUserStatus($userId, 'active');
                    $message = 'User activated successfully.';
                    break;
                    
                case 'suspend':
                    $userModel->updateUserStatus($userId, 'suspended');
                    $message = 'User suspended successfully.';
                    break;
                    
                case 'delete':
                    $userModel->deleteUser($userId);
                    $message = 'User deleted successfully.';
                    break;
                    
                default:
                    $error = 'Invalid action.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}


$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';


$users = $userModel->getAllUsers($roleFilter, $statusFilter);

require_once __DIR__ . '/../View/admin/users-view.php';


?>

