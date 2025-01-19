<?php

namespace App\Controller;

use App\Model\UserModel;
use Exception;

class UsersPageController
{
    private UserModel $userModel;
    private string $message = '';
    private string $error = '';
    
    public function __construct()
    {
        session_start();
        $this->userModel = new UserModel();
    }
    
    public function checkAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function handleRequest(): void
    {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        }
        
        $this->displayPage();
    }
    
    private function handlePostRequest(): void
    {
        $action = $_POST['action'] ?? '';
        $userId = $_POST['user_id'] ?? '';
        
        if (!empty($userId)) {
            try {
                switch ($action) {
                    case 'activate':
                        $this->userModel->updateUserStatus($userId, 'active');
                        $this->message = 'User activated successfully.';
                        break;
                        
                    case 'suspend':
                        $this->userModel->updateUserStatus($userId, 'suspended');
                        $this->message = 'User suspended successfully.';
                        break;
                        
                    case 'delete':
                        $this->userModel->deleteUser($userId);
                        $this->message = 'User deleted successfully.';
                        break;
                        
                    default:
                        $this->error = 'Invalid action.';
                }
            } catch (Exception $e) {
                $this->error = 'An error occurred: ' . $e->getMessage();
            }
        }
    }
    
    private function displayPage(): void
    {
        $roleFilter = $_GET['role'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $users = $this->userModel->getAllUsers($roleFilter, $statusFilter);
        
        // Make variables available to the view
        $viewData = [
            'users' => $users,
            'message' => $this->message,
            'error' => $this->error,
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter
        ];
        
        extract($viewData);
        require_once __DIR__ . '/../View/admin/users-view.php';
    }
    
    // Getter methods for testing and external access if needed
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getError(): string
    {
        return $this->error;
    }
}