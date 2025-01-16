<?php
require_once '../../vendor/autoload.php';


session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
    exit();
}

// Initialize CourseModel
$courseModel = new App\Model\VideoCourse();

// Get course ID from URL
$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$courseId) {
    header('Location: ../pages/student-page.php');
    exit();
}

// Fetch course details with content
$course = $courseModel->getCourseWithContent($courseId);

if (!$course) {
    header('Location: ../View/student/student-page.php');
    exit();
}

// Check if user is enrolled in this course
$isEnrolled = false;
if (isset($_SESSION['user']['id'])) {
    $enrolledCourses = $courseModel->getStudentCourses($_SESSION['user']['id']);
    $isEnrolled = array_filter($enrolledCourses, function($c) use ($courseId) {
        return $c['id'] == $courseId;
    });
}

require_once __DIR__ . '/../View/student/course-page.php';


?>
