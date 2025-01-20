<?php
session_start();
require_once '../../vendor/autoload.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: /login.php');
    exit;
}

$courseModel = new \App\Model\VideoCourse();
$categoryModel = new \App\Model\CategoryModel();


$search = $_GET['search'] ?? '';
$view = $_GET['view'] ?? 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;

$studentId = $_SESSION['user']['id'];


$courses = $courseModel->getCourses($page, $limit, $search);
$totalCourses = $courseModel->getTotalCourses($search);
$totalPages = ceil($totalCourses / $limit);


$categories = $categoryModel->getAllCategories();


if ($view === 'enrolled' || $view === 'all') {
    $enrolledCourses = $courseModel->getEnrolledCoursesForStudent($studentId, $search, $page, $limit);
}

if ($view === 'available' || $view === 'all') {
    $availableCourses = $courseModel->getAvailableCoursesForStudent($studentId, $search, $page, $limit);
}

require_once __DIR__ . '/../View/student/student-page.php';
?>