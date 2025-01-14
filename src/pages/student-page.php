<?php

session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\CourseModel;


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: /login.php');
    exit;
}

$courseModel = new CourseModel();
$enrolledCourses = $courseModel->getStudentCourses($_SESSION['user']['id']);
$availableCourses = $courseModel->getAvailableCourses($_SESSION['user']['id']);


require_once __DIR__ . '/../View/student/student-page.php';
?>