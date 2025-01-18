<?php
session_start();
require_once '../../vendor/autoload.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: /login.php');
    exit;
}


if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    $_SESSION['error'] = 'Invalid course selection.';
    header('Location: ../pages/student-page.php');
    exit;
}

$courseId = (int)$_POST['course_id'];
$studentId = $_SESSION['user']['id'];


$courseModel = new App\Model\VideoCourse();

try {

    $course = $courseModel->getCourseById($courseId);
    if (!$course) {
        throw new Exception('Course not found.');
    }


    $enrolledCourses = $courseModel->getStudentCourses($studentId);
    $isEnrolled = array_filter($enrolledCourses, function($c) use ($courseId) {
        return $c['id'] == $courseId;
    });

    if (!empty($isEnrolled)) {
        throw new Exception('You are already enrolled in this course.');
    }


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