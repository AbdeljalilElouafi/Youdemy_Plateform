<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Model\VideoCourse;


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

$courseModel = new VideoCourse();
$statistics = $courseModel->getCourseStatistics($_SESSION['user']['id']);
$courses = $courseModel->getTeacherCourses($_SESSION['user']['id']);


require_once __DIR__ . '/../View/teacher/teacher-page.php';
?>