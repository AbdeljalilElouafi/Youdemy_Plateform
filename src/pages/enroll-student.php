<?php
require_once '../../vendor/autoload.php';
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: /login.php');
    exit;
}

// Check if course_id was provided
if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    $_SESSION['error'] = 'Invalid course selection.';
    header('Location: ../pages/student-page.php');
    exit;
}

$courseId = (int)$_POST['course_id'];
$studentId = $_SESSION['user']['id'];

// Initialize course model
$courseModel = new App\Model\VideoCourse();

try {
    // Check if course exists
    $course = $courseModel->getCourseById($courseId);
    if (!$course) {
        throw new Exception('Course not found.');
    }

    // Check if student is already enrolled
    $enrolledCourses = $courseModel->getStudentCourses($studentId);
    $isEnrolled = array_filter($enrolledCourses, function($c) use ($courseId) {
        return $c['id'] == $courseId;
    });

    if (!empty($isEnrolled)) {
        throw new Exception('You are already enrolled in this course.');
    }

    // Enroll the student
    $enrolled = $courseModel->enrollStudent($courseId, $studentId);

    if ($enrolled) {
        $_SESSION['success'] = 'Successfully enrolled in the course!';
        header('Location: ../pages/course-page.php?id=' . $courseId);
        exit;
    } else {
        throw new Exception('Failed to enroll in the course. Please try again.');
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../pages/student-page.php');
    exit;
}