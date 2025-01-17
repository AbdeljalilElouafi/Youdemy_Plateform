<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}


if (isset($_GET['id']) ? $_GET['id'] : null) {

    $course_id = $_GET['id'];
    
    $courseModel = new App\Model\VideoCourse(); 
    
    $courseModel->deleteCourse($course_id);
    echo "Course deleted successfully!";
    header('Location: teacher-page.php');
    exit;
    
} else {
    echo "Course ID is required.";
    exit;
}