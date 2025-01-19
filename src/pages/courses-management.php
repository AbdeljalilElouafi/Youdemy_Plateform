<?php
session_start();
require_once '../../vendor/autoload.php';

$courseModel = new \App\Model\VideoCourse();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$message = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $courseId = $_POST['course_id'] ?? '';
    
    if (!empty($courseId)) {
        try {
            switch ($action) {
                case 'publish':
                    $courseModel->updateCourse($courseId, [
                        'status' => 'published',
                        'published_at' => date('Y-m-d H:i:s')
                    ]);
                    $message = 'Course published successfully.';
                    break;
                    
                case 'archive':
                    $courseModel->updateCourse($courseId, [
                        'status' => 'archived'
                    ]);
                    $message = 'Course archived successfully.';
                    break;
                    
                case 'draft':
                    $courseModel->updateCourse($courseId, [
                        'status' => 'draft',
                        'published_at' => null
                    ]);
                    $message = 'Course moved to draft successfully.';
                    break;
                    
                default:
                    $error = 'Invalid action.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}


$statusFilter = $_GET['status'] ?? '';
$categoryId = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 30;


try {

    $courses = $courseModel->searchAllCourses($search, $page, $limit, $categoryId, $statusFilter);
    

    $categoryModel = new \App\Model\CategoryModel();
    $categories = $categoryModel->getAllCategories();
    
} catch (Exception $e) {
    $error = 'Error fetching courses: ' . $e->getMessage();
    $courses = [];
    $categories = [];
}


require_once __DIR__ . '/../View/admin/courses-view.php';